<?php

namespace Tests\Feature;

use App\Http\Requests\StoreBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    private Author $author;

    private Category $category;

    private Publisher $publisher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->publisher = Publisher::create(['name' => 'NXB Kiểm thử']);
        $this->author = Author::create(['name' => 'Nguyễn Văn An']);
        $this->category = Category::create(['name' => 'Công nghệ thông tin']);

        // Route tạm để kiểm tra hàm lấy dữ liệu của Request đúng như Controller sẽ gọi
        Route::post('/_test/book-request', fn (StoreBookRequest $request) => response()->json([
            'data' => $request->bookData(),
        ]));
    }

    public function test_book_data_excludes_relations_and_keeps_cover_url(): void
    {
        $response = $this->postJson('/_test/book-request', $this->validData([
            'page_count' => 320,
            'dimensions' => '16 x 24 cm',
            'cover_image_url' => 'https://example.com/bia.png',
        ]))->assertOk();

        $data = $response->json('data');
        $this->assertSame('Sách kiểm thử', $data['title']);
        $this->assertSame(320, $data['page_count']);
        $this->assertSame('https://example.com/bia.png', $data['cover_image_url']);
        $this->assertArrayNotHasKey('author_ids', $data);
        $this->assertArrayNotHasKey('category_ids', $data);
    }

    #[DataProvider('invalidCoverUrls')]
    public function test_cover_image_url_must_be_a_valid_http_link(string $url, string $message): void
    {
        $this->post(route('books.store'), $this->validData(['cover_image_url' => $url]))
            ->assertSessionHasErrors(['cover_image_url' => $message]);

        $this->assertDatabaseCount('books', 0);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function invalidCoverUrls(): array
    {
        return [
            'not a url' => ['abc', 'Link ảnh bìa phải là địa chỉ http:// hoặc https:// hợp lệ.'],
            'ftp scheme' => ['ftp://example.com/bia.jpg', 'Link ảnh bìa phải là địa chỉ http:// hoặc https:// hợp lệ.'],
            'too long' => ['https://example.com/'.str_repeat('a', 240), 'Link ảnh bìa không được vượt quá 255 ký tự.'],
        ];
    }

    public function test_page_count_and_dimensions_show_vietnamese_messages(): void
    {
        $this->post(route('books.store'), $this->validData([
            'page_count' => 0,
            'dimensions' => str_repeat('x', 51),
        ]))->assertSessionHasErrors([
            'page_count' => 'Số trang phải lớn hơn 0.',
            'dimensions' => 'Kích thước không được vượt quá 50 ký tự.',
        ]);

        $this->post(route('books.store'), $this->validData(['page_count' => 'abc']))
            ->assertSessionHasErrors(['page_count' => 'Số trang phải là số nguyên.']);
    }

    public function test_publisher_and_published_year_are_required_when_creating(): void
    {
        $data = $this->validData();
        unset($data['publisher_id'], $data['published_year']);

        $this->post(route('books.store'), $data)->assertSessionHasErrors([
            'publisher_id' => 'Nhà xuất bản là bắt buộc.',
            'published_year' => 'Năm xuất bản là bắt buộc.',
        ]);

        $this->assertDatabaseCount('books', 0);
    }

    public function test_published_year_cannot_be_in_the_future(): void
    {
        $currentYear = now()->year;

        $this->post(route('books.store'), $this->validData(['published_year' => $currentYear + 1]))
            ->assertSessionHasErrors(['published_year' => "Năm xuất bản không được lớn hơn năm hiện tại ({$currentYear})."]);

        $this->post(route('books.store'), $this->validData(['published_year' => 1900]))
            ->assertSessionHasErrors(['published_year' => 'Năm xuất bản phải từ 1901 trở về sau.']);

        $this->post(route('books.store'), $this->validData(['published_year' => $currentYear]))
            ->assertSessionHasNoErrors();

        $this->assertSame($currentYear, Book::firstOrFail()->published_year);
    }

    public function test_isbn_is_normalized_and_blank_selections_are_ignored(): void
    {
        $this->post(route('books.store'), $this->validData([
            'isbn' => ' 978-604-999 9999 ',
            'author_ids' => ['', (string) $this->author->id],
            'category_ids' => [(string) $this->category->id, ''],
        ]))->assertSessionHasNoErrors();

        $book = Book::firstOrFail();
        $this->assertSame('9786049999999', $book->isbn);
        $this->assertSame([$this->author->id], $book->authors->modelKeys());
        $this->assertSame([$this->category->id], $book->categories->modelKeys());
    }

    public function test_only_blank_selections_count_as_missing(): void
    {
        $this->post(route('books.store'), $this->validData(['author_ids' => [''], 'category_ids' => ['']]))
            ->assertSessionHasErrors([
                'author_ids' => 'Tác giả là bắt buộc.',
                'category_ids' => 'Thể loại là bắt buộc.',
            ]);
    }

    public function test_existing_isbn_cannot_be_changed_or_cleared_on_update(): void
    {
        $book = $this->createBook('9786041234567');
        $message = 'Mã sách / ISBN không được thay đổi khi sửa sách.';

        $this->put(route('books.update', $book), $this->validData(['isbn' => '9786040000000']))
            ->assertSessionHasErrors(['isbn' => $message]);

        $this->put(route('books.update', $book), $this->validData(['isbn' => '']))
            ->assertSessionHasErrors(['isbn' => $message]);

        $this->assertSame('9786041234567', $book->fresh()->isbn);
    }

    public function test_unchanged_or_omitted_isbn_passes_on_update(): void
    {
        $book = $this->createBook('9786041234567');

        $this->put(route('books.update', $book), $this->validData(['isbn' => '978-604-123-4567', 'title' => 'Đổi tên']))
            ->assertSessionHasNoErrors();

        $data = $this->validData(['title' => 'Không gửi ISBN']);
        unset($data['isbn']);
        $this->put(route('books.update', $book), $data)->assertSessionHasNoErrors();

        $book->refresh();
        $this->assertSame('Không gửi ISBN', $book->title);
        $this->assertSame('9786041234567', $book->isbn);
    }

    public function test_book_without_isbn_can_receive_a_unique_one_on_update(): void
    {
        $this->createBook('9786041234567');
        $book = $this->createBook(null);

        $this->put(route('books.update', $book), $this->validData(['isbn' => '9786041234567']))
            ->assertSessionHasErrors(['isbn' => 'Mã sách / ISBN đã tồn tại.']);

        $this->put(route('books.update', $book), $this->validData(['isbn' => '9786047654321']))
            ->assertSessionHasNoErrors();

        $this->assertSame('9786047654321', $book->fresh()->isbn);
    }

    public function test_store_and_update_save_cover_url(): void
    {
        $this->post(route('books.store'), $this->validData(['cover_image_url' => 'https://example.com/bia.jpg']))
            ->assertSessionHasNoErrors();

        $book = Book::firstOrFail();
        $this->assertSame('https://example.com/bia.jpg', $book->cover_image_url);

        $this->put(route('books.update', $book), $this->validData([
            'isbn' => $book->isbn,
            'cover_image_url' => 'https://example.com/moi.png',
        ]))->assertSessionHasNoErrors();

        $this->assertSame('https://example.com/moi.png', $book->fresh()->cover_image_url);
    }

    public function test_forms_offer_cover_url_input_and_lock_existing_isbn(): void
    {
        $book = $this->createBook('9786041234567');

        $this->get(route('books.create'))
            ->assertOk()
            ->assertSee('name="cover_image_url"', false)
            ->assertDontSee('multipart/form-data', false)
            ->assertDontSee('readonly', false);

        $this->get(route('books.edit', $book))
            ->assertOk()
            ->assertSee('name="cover_image_url"', false)
            ->assertSee('readonly', false);
    }

    private function createBook(?string $isbn): Book
    {
        $book = Book::create(['title' => 'Sách ban đầu', 'isbn' => $isbn, 'price' => 100000]);
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
            'price' => '125000',
            'stock_quantity' => 0,
        ], $overrides);
    }
}
