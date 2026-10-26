<?php

namespace Tests\Feature;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Kiểm tra StoreBookRequest / UpdateBookRequest qua route tạm (chỉ tồn tại trong test),
 * để chạy đúng luồng thật: prepareForValidation -> rules -> lấy dữ liệu.
 */
class BookRequestValidationTest extends TestCase
{
    use RefreshDatabase;

    private Publisher $publisher;

    private Author $author;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $result = fn (StoreBookRequest $request) => [
            'book' => $request->bookData(),
            'authors' => $request->authorIds(),
            'categories' => $request->categoryIds(),
        ];
        Route::post('/_test/books', $result);
        Route::put('/_test/books/{book}', fn (UpdateBookRequest $request, Book $book) => $result($request));

        $this->publisher = Publisher::create(['name' => 'Kim Đồng']);
        $this->author = Author::create(['name' => 'Tô Hoài']);
        $this->category = Category::create(['name' => 'Văn Học']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Dế Mèn Phiêu Lưu Ký',
            'isbn' => '9786042000001',
            'price' => 55000,
            'published_year' => 2020,
            'page_count' => 144,
            'dimensions' => '13 x 20.5 cm',
            'publisher_id' => $this->publisher->id,
            'author_ids' => [$this->author->id],
            'category_ids' => [$this->category->id],
        ], $overrides);
    }

    public function test_valid_data_passes_and_is_split_for_controller(): void
    {
        $this->postJson('/_test/books', $this->payload())
            ->assertOk()
            ->assertJsonPath('book.title', 'Dế Mèn Phiêu Lưu Ký')
            ->assertJsonPath('book.isbn', '9786042000001')
            ->assertJsonMissingPath('book.author_ids')
            ->assertJsonPath('authors', [$this->author->id])
            ->assertJsonPath('categories', [$this->category->id]);
    }

    public function test_required_fields_have_vietnamese_messages(): void
    {
        $this->postJson('/_test/books', $this->payload([
            'title' => '',
            'price' => '',
            'author_ids' => [],
            'category_ids' => [],
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title' => 'Vui lòng nhập tên sách.',
                'price' => 'Vui lòng nhập giá bán.',
                'author_ids' => 'Vui lòng chọn ít nhất một tác giả.',
                'category_ids' => 'Vui lòng chọn ít nhất một thể loại.',
            ]);
    }

    public function test_numbers_and_lengths_are_limited(): void
    {
        $this->postJson('/_test/books', $this->payload([
            'title' => str_repeat('a', 256),
            'price' => -1,
            'published_year' => 1800,
            'page_count' => 0,
            'dimensions' => str_repeat('x', 51),
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title' => 'Tên sách không được dài quá 255 ký tự.',
                'price' => 'Giá bán phải lớn hơn hoặc bằng 0.',
                'published_year' => 'Năm xuất bản phải lớn hơn hoặc bằng 1901.',
                'page_count',
                'dimensions',
            ]);
    }

    public function test_foreign_keys_must_exist_and_not_repeat(): void
    {
        $this->postJson('/_test/books', $this->payload([
            'publisher_id' => 999,
            'author_ids' => [$this->author->id, $this->author->id],
            'category_ids' => [999],
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'publisher_id' => 'Nhà xuất bản đã chọn không tồn tại.',
                'author_ids.0' => 'Tác giả bị chọn trùng.',
                'category_ids.0' => 'Thể loại đã chọn không tồn tại.',
            ]);
    }

    public function test_empty_selects_are_ignored_and_isbn_is_normalized(): void
    {
        $this->postJson('/_test/books', $this->payload([
            'isbn' => '978-604-200 0001',
            'author_ids' => ['', $this->author->id, null],
            'category_ids' => [$this->category->id, ''],
        ]))
            ->assertOk()
            ->assertJsonPath('book.isbn', '9786042000001')
            ->assertJsonPath('authors', [$this->author->id])
            ->assertJsonPath('categories', [$this->category->id]);
    }

    public function test_isbn_must_be_unique(): void
    {
        Book::create(['title' => 'Sách có sẵn', 'isbn' => '111', 'price' => 0]);

        $this->postJson('/_test/books', $this->payload(['isbn' => '111']))
            ->assertJsonValidationErrors(['isbn' => 'Mã sách (ISBN) này đã tồn tại.']);
    }

    public function test_cover_must_be_an_image_up_to_2mb(): void
    {
        $this->postJson('/_test/books', $this->payload([
            'cover_image' => UploadedFile::fake()->create('bia.pdf', 100, 'application/pdf'),
        ]))->assertJsonValidationErrors(['cover_image' => 'Ảnh bìa phải là file ảnh.']);

        $this->postJson('/_test/books', $this->payload([
            'cover_image' => UploadedFile::fake()->image('bia.jpg')->size(3000),
        ]))->assertJsonValidationErrors(['cover_image' => 'Ảnh bìa không được lớn hơn 2048 KB.']);

        $this->postJson('/_test/books', $this->payload([
            'cover_image' => UploadedFile::fake()->image('bia.png'),
        ]))->assertOk();
    }

    public function test_update_ignores_isbn(): void
    {
        $book = Book::create(['title' => 'Sách cũ', 'isbn' => '111', 'price' => 0]);
        Book::create(['title' => 'Sách khác', 'isbn' => '222', 'price' => 0]);

        // Gửi ISBN trùng sách khác cũng không lỗi, vì khi sửa ISBN bị bỏ qua hoàn toàn
        $this->putJson("/_test/books/{$book->id}", $this->payload(['isbn' => '222']))
            ->assertOk()
            ->assertJsonMissingPath('book.isbn')
            ->assertJsonPath('book.title', 'Dế Mèn Phiêu Lưu Ký');
    }
}
