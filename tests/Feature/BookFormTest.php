<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookFormTest extends TestCase
{
    use RefreshDatabase;

    private Author $author;

    private Category $category;

    private Publisher $publisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->startSession();
        $this->withCookie(config('session.cookie'), session()->getId());
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->author = Author::create(['name' => 'Nguyễn Văn An']);
        $this->category = Category::create(['name' => 'Công nghệ thông tin']);
        $this->publisher = Publisher::create(['name' => 'NXB Giáo dục']);
    }

    public function test_create_form_shows_catalogue_options_and_initial_out_of_stock_status(): void
    {
        $this->get(route('books.create'))
            ->assertOk()
            ->assertSee('Thêm sách mới')
            ->assertSee($this->author->name)
            ->assertSee($this->category->name)
            ->assertSee($this->publisher->name)
            ->assertSee('Hết hàng')
            ->assertSee('name="author_ids[]"', false)
            ->assertSee('name="category_ids[]"', false)
            ->assertSee('book-form.css')
            ->assertSee('book-form.js');
    }

    public function test_edit_form_prefills_book_values_and_relationships(): void
    {
        $book = $this->createBook();

        $response = $this->get(route('books.edit', $book))
            ->assertOk()
            ->assertSee('Chỉnh sửa sách')
            ->assertSee('Cập nhật thông tin sách')
            ->assertSee('Cập nhật sách')
            ->assertSee('value="Sách ban đầu"', false)
            ->assertSee('value="9786041234567"', false)
            ->assertSee('Hết hàng');

        $this->assertSame(2, substr_count($response->getContent(), 'checked'));
    }

    public function test_create_saves_multiple_relationships_and_flashes_success(): void
    {
        $secondAuthor = Author::create(['name' => 'Tác giả thứ hai']);
        $secondCategory = Category::create(['name' => 'Tham khảo']);
        $data = $this->validData([
            'author_ids' => [$this->author->id, $secondAuthor->id],
            'category_ids' => [$this->category->id, $secondCategory->id],
        ]);

        $response = $this->post(route('books.store'), $data);
        $book = Book::firstOrFail();

        $response->assertRedirect(route('books.edit', $book))
            ->assertSessionHas('success', 'Đã thêm sách mới thành công.');
        $this->assertSame('Sách kiểm thử', $book->title);
        $this->assertSame(0, $book->stock_quantity);
        $this->assertSame(2, $book->authors()->count());
        $this->assertSame(2, $book->categories()->count());

        $this->get(route('books.edit', $book))
            ->assertOk()
            ->assertSee('Đã thêm sách mới thành công.')
            ->assertSee('Hết hàng');
    }

    public function test_empty_create_shows_vietnamese_banner_and_field_errors(): void
    {
        $this->from(route('books.create'))
            ->post(route('books.store'), [])
            ->assertRedirect(route('books.create'))
            ->assertSessionHasErrors(['title', 'author_ids', 'category_ids', 'price', 'stock_quantity']);

        $this->get(route('books.create'))
            ->assertOk()
            ->assertSee('Không thể lưu sách')
            ->assertSee('Tên sách là bắt buộc.')
            ->assertSee('Tác giả là bắt buộc.')
            ->assertSee('Thể loại là bắt buộc.')
            ->assertSee('aria-invalid="true"', false);

        $this->assertDatabaseCount('books', 0);
    }

    public function test_invalid_create_keeps_entered_values_and_all_selected_options(): void
    {
        $secondAuthor = Author::create(['name' => 'Tác giả thứ hai']);
        $data = $this->validData([
            'title' => 'Giữ nguyên nội dung đã nhập',
            'stock_quantity' => -1,
            'author_ids' => [$this->author->id, $secondAuthor->id],
        ]);

        $this->from(route('books.create'))
            ->post(route('books.store'), $data)
            ->assertSessionHasErrors('stock_quantity')
            ->assertSessionHasInput('title', $data['title'])
            ->assertSessionHasInput('author_ids', $data['author_ids']);

        $response = $this->get(route('books.create'))
            ->assertOk()
            ->assertSee('value="Giữ nguyên nội dung đã nhập"', false)
            ->assertSee('Số lượng tồn kho không được âm.');

        $this->assertSame(3, substr_count($response->getContent(), 'checked'));
        $this->assertDatabaseCount('books', 0);
    }

    public function test_update_changes_stock_and_replaces_relationships_without_losing_other_metadata(): void
    {
        $book = $this->createBook();
        $book->update([
            'stock_quantity' => 12,
            'page_count' => 240,
            'dimensions' => '16 x 24 cm',
            'cover_image_url' => 'covers/example.jpg',
        ]);
        $replacementAuthor = Author::create(['name' => 'Tác giả mới']);
        $replacementCategory = Category::create(['name' => 'Văn học']);

        $this->put(route('books.update', $book), $this->validData([
            'title' => 'Sách đã cập nhật',
            'isbn' => $book->isbn,
            'stock_quantity' => 0,
            'author_ids' => [$replacementAuthor->id],
            'category_ids' => [$replacementCategory->id],
            'publisher_id' => null,
        ]))->assertRedirect(route('books.edit', $book))
            ->assertSessionHas('success', 'Đã cập nhật sách thành công.');

        $book->refresh();
        $this->assertSame('Sách đã cập nhật', $book->title);
        $this->assertSame(0, $book->stock_quantity);
        $this->assertSame([$replacementAuthor->id], $book->authors->modelKeys());
        $this->assertSame([$replacementCategory->id], $book->categories->modelKeys());
        $this->assertNull($book->publisher_id);
        $this->assertSame(240, $book->page_count);
        $this->assertSame('16 x 24 cm', $book->dimensions);
        $this->assertSame('covers/example.jpg', $book->cover_image_url);
    }

    public function test_failed_edit_keeps_deselected_relationships_empty_and_does_not_save(): void
    {
        $book = $this->createBook();
        $data = $this->validData(['isbn' => $book->isbn]);
        unset($data['author_ids'], $data['category_ids']);

        $this->from(route('books.edit', $book))
            ->put(route('books.update', $book), $data)
            ->assertSessionHasErrors(['author_ids', 'category_ids']);

        $response = $this->get(route('books.edit', $book))
            ->assertOk()
            ->assertSee('Không thể cập nhật sách')
            ->assertSee('Tác giả là bắt buộc.');

        $this->assertSame(0, substr_count($response->getContent(), 'checked'));
        $this->assertSame('Sách ban đầu', $book->fresh()->title);
        $this->assertSame([$this->author->id], $book->fresh()->authors->modelKeys());
    }

    public function test_duplicate_isbn_on_create_and_update_is_rejected(): void
    {
        $first = $this->createBook();
        $second = Book::create(['title' => 'Sách khác', 'isbn' => '9786047654321', 'price' => 0]);

        $this->post(route('books.store'), $this->validData(['isbn' => $first->isbn]))
            ->assertSessionHasErrors('isbn');

        $this->put(route('books.update', $second), $this->validData(['isbn' => $first->isbn]))
            ->assertSessionHasErrors('isbn');

        $this->assertSame('9786047654321', $second->fresh()->isbn);
        $this->assertDatabaseCount('books', 2);
    }

    #[DataProvider('invalidFieldValues')]
    public function test_invalid_numbers_and_relations_are_rejected(string $field, mixed $value, string $error): void
    {
        $this->post(route('books.store'), $this->validData([$field => $value]))
            ->assertSessionHasErrors($error);

        $this->assertDatabaseCount('books', 0);
        $this->assertDatabaseCount('author_book', 0);
        $this->assertDatabaseCount('book_category', 0);
    }

    /**
     * @return array<string, array{string, mixed, string}>
     */
    public static function invalidFieldValues(): array
    {
        return [
            'negative stock' => ['stock_quantity', -1, 'stock_quantity'],
            'fractional stock' => ['stock_quantity', 1.5, 'stock_quantity'],
            'overflow stock' => ['stock_quantity', 4294967296, 'stock_quantity'],
            'negative price' => ['price', -100, 'price'],
            'too many decimal places' => ['price', '12.345', 'price'],
            'unsupported publication year' => ['published_year', 9999, 'published_year'],
            'missing author' => ['author_ids', [999999], 'author_ids.0'],
            'missing category' => ['category_ids', [999999], 'category_ids.0'],
            'missing publisher' => ['publisher_id', 999999, 'publisher_id'],
            'duplicate authors' => ['author_ids', [1, 1], 'author_ids.0'],
        ];
    }

    public function test_missing_or_deleted_books_cannot_be_edited_or_updated(): void
    {
        $this->get(route('books.edit', 999999))->assertNotFound();
        $this->put(route('books.update', 999999), $this->validData())->assertNotFound();

        $book = $this->createBook();
        $book->delete();

        $this->get(route('books.edit', $book))->assertNotFound();
        $this->put(route('books.update', $book), $this->validData())->assertNotFound();
    }

    private function createBook(): Book
    {
        $book = Book::create([
            'title' => 'Sách ban đầu',
            'isbn' => '9786041234567',
            'price' => 100000,
            'stock_quantity' => 0,
            'publisher_id' => $this->publisher->id,
        ]);
        $book->authors()->attach($this->author);
        $book->categories()->attach($this->category);

        return $book;
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Sách kiểm thử',
            'isbn' => '9786049999999',
            'author_ids' => [$this->author->id],
            'category_ids' => [$this->category->id],
            'publisher_id' => $this->publisher->id,
            'published_year' => 2024,
            'price' => '125000.50',
            'stock_quantity' => 0,
            'description' => 'Mô tả dùng để kiểm thử form.',
        ], $overrides);
    }
}
