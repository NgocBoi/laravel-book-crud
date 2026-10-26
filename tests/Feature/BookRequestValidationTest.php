<?php

namespace Tests\Feature;

use App\Http\Requests\StoreBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class BookRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    private Author $author;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->author = Author::create(['name' => 'Nguyễn Văn An']);
        $this->category = Category::create(['name' => 'Công nghệ thông tin']);

        // Route tạm để kiểm tra hàm lấy dữ liệu của Request đúng như Controller sẽ gọi
        Route::post('/_test/book-request', fn (StoreBookRequest $request) => response()->json([
            'data' => $request->bookData(),
            'cover' => $request->storeCover(),
        ]));
    }

    public function test_book_data_excludes_relations_and_valid_cover_is_stored(): void
    {
        Storage::fake('public');

        $response = $this->postJson('/_test/book-request', $this->validData([
            'page_count' => 320,
            'dimensions' => '16 x 24 cm',
            'cover_image' => UploadedFile::fake()->image('bia.png', 300, 450),
        ]))->assertOk();

        $data = $response->json('data');
        $this->assertSame('Sách kiểm thử', $data['title']);
        $this->assertSame(320, $data['page_count']);
        $this->assertArrayNotHasKey('author_ids', $data);
        $this->assertArrayNotHasKey('category_ids', $data);
        $this->assertArrayNotHasKey('cover_image', $data);

        $cover = $response->json('cover');
        $this->assertStringStartsWith(Book::COVER_DIR.'/', $cover);
        $this->assertNotSame(Book::COVER_DIR.'/bia.png', $cover);
        Storage::disk('public')->assertExists($cover);
    }

    public function test_store_cover_returns_null_without_upload(): void
    {
        $this->postJson('/_test/book-request', $this->validData())
            ->assertOk()
            ->assertJsonPath('cover', null);
    }

    #[DataProvider('invalidCovers')]
    public function test_cover_image_must_be_supported_image_up_to_2mb(callable $makeFile, string $message): void
    {
        Storage::fake('public');

        $this->post(route('books.store'), $this->validData(['cover_image' => $makeFile()]))
            ->assertSessionHasErrors(['cover_image' => $message]);

        $this->assertDatabaseCount('books', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    /**
     * @return array<string, array{callable(): UploadedFile, string}>
     */
    public static function invalidCovers(): array
    {
        return [
            'pdf file' => [fn () => UploadedFile::fake()->create('bia.pdf', 100, 'application/pdf'), 'Ảnh bìa phải là file hình ảnh.'],
            'gif image' => [fn () => UploadedFile::fake()->image('bia.gif'), 'Ảnh bìa chỉ nhận định dạng JPG, JPEG, PNG hoặc WEBP.'],
            'larger than 2MB' => [fn () => UploadedFile::fake()->image('bia.jpg')->size(2049), 'Ảnh bìa không được vượt quá 2MB.'],
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

    public function test_store_saves_uploaded_cover(): void
    {
        Storage::fake('public');

        $this->post(route('books.store'), $this->validData([
            'cover_image' => UploadedFile::fake()->image('bia.jpg'),
        ]))->assertSessionHasNoErrors();

        $cover = Book::firstOrFail()->cover_image_url;
        $this->assertStringStartsWith(Book::COVER_DIR.'/', $cover);
        Storage::disk('public')->assertExists($cover);
    }

    public function test_update_replaces_cover_and_deletes_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('covers/cu.jpg', 'ảnh cũ');
        $book = $this->createBook('9786041234567');
        $book->update(['cover_image_url' => 'covers/cu.jpg']);

        $this->put(route('books.update', $book), $this->validData([
            'isbn' => $book->isbn,
            'cover_image' => UploadedFile::fake()->image('moi.png'),
        ]))->assertSessionHasNoErrors();

        $newCover = $book->fresh()->cover_image_url;
        $this->assertNotSame('covers/cu.jpg', $newCover);
        Storage::disk('public')->assertExists($newCover);
        Storage::disk('public')->assertMissing('covers/cu.jpg');
    }

    public function test_update_without_new_cover_keeps_current_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('covers/cu.jpg', 'ảnh cũ');
        $book = $this->createBook('9786041234567');
        $book->update(['cover_image_url' => 'covers/cu.jpg']);

        $this->put(route('books.update', $book), $this->validData(['isbn' => $book->isbn]))
            ->assertSessionHasNoErrors();

        $this->assertSame('covers/cu.jpg', $book->fresh()->cover_image_url);
        Storage::disk('public')->assertExists('covers/cu.jpg');
    }

    public function test_forms_accept_cover_upload_and_lock_existing_isbn(): void
    {
        $book = $this->createBook('9786041234567');

        $this->get(route('books.create'))
            ->assertOk()
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="cover_image"', false)
            ->assertDontSee('readonly', false);

        $this->get(route('books.edit', $book))
            ->assertOk()
            ->assertSee('name="cover_image"', false)
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
            'price' => '125000',
            'stock_quantity' => 0,
        ], $overrides);
    }
}
