<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_store_creates_book_with_relations_and_cover(): void
    {
        $publisher = Publisher::create(['name' => 'NXB Trẻ']);
        $authors = new Collection([Author::create(['name' => 'A']), Author::create(['name' => 'B'])]);
        $category = Category::create(['name' => 'Văn học']);

        $response = $this->post(route('books.store'), [
            'isbn' => '9786040000001',
            'title' => 'Sách mới',
            'price' => 120000,
            'published_year' => 2024,
            'page_count' => 300,
            'publisher_id' => $publisher->id,
            'author_ids' => $authors->modelKeys(),
            'category_ids' => [$category->id],
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
        ]);

        $book = Book::firstWhere('isbn', '9786040000001');

        $response->assertRedirect(route('books.show', $book))->assertSessionHas('success');
        $this->assertSame($publisher->id, $book->publisher_id);
        $this->assertEqualsCanonicalizing($authors->modelKeys(), $book->authors->modelKeys());
        $this->assertSame([$category->id], $book->categories->modelKeys());
        $this->assertStringStartsWith('books/', $book->cover_image_url);
        Storage::disk('public')->assertExists($book->cover_image_url);
    }

    public function test_store_validates_input(): void
    {
        Book::create(['isbn' => '111', 'title' => 'Cũ', 'price' => 1]);

        $this->post(route('books.store'), [
            'isbn' => '111',
            'title' => '',
            'price' => -5,
            'published_year' => 1800,
            'publisher_id' => 999,
            'author_ids' => [999],
            'cover_image' => UploadedFile::fake()->create('file.pdf', 10, 'application/pdf'),
        ])->assertSessionHasErrors([
            'isbn', 'title', 'price', 'published_year', 'publisher_id', 'author_ids.0', 'cover_image',
        ]);

        $this->assertSame(1, Book::count());
    }

    public function test_store_ignores_empty_author_and_category_selects(): void
    {
        $author = Author::create(['name' => 'A']);

        $this->post(route('books.store'), [
            'title' => 'Không thể loại',
            'price' => 1000,
            'author_ids' => ['', $author->id, ''],
            'category_ids' => [''],
        ])->assertSessionHasNoErrors();

        $book = Book::firstWhere('title', 'Không thể loại');
        $this->assertSame([$author->id], $book->authors->modelKeys());
        $this->assertCount(0, $book->categories);
    }

    public function test_update_changes_data_replaces_cover_and_keeps_isbn(): void
    {
        $oldCover = UploadedFile::fake()->image('old.jpg')->store('books', 'public');
        $book = Book::create(['isbn' => '222', 'title' => 'Cũ', 'price' => 1, 'cover_image_url' => $oldCover]);
        $book->authors()->attach(Author::create(['name' => 'A']));
        $category = Category::create(['name' => 'Thiếu nhi']);

        $this->put(route('books.update', $book), [
            'isbn' => '999',
            'title' => 'Mới',
            'price' => 50000,
            'category_ids' => [$category->id],
            'cover_image' => UploadedFile::fake()->image('new.png'),
        ])->assertRedirect(route('books.show', $book))->assertSessionHas('success');

        $book->refresh();
        $this->assertSame('222', $book->isbn);
        $this->assertSame('Mới', $book->title);
        $this->assertCount(0, $book->authors);
        $this->assertSame([$category->id], $book->categories->modelKeys());
        Storage::disk('public')->assertMissing($oldCover);
        Storage::disk('public')->assertExists($book->cover_image_url);
    }

    public function test_update_without_new_cover_keeps_old_cover(): void
    {
        $cover = UploadedFile::fake()->image('old.jpg')->store('books', 'public');
        $book = Book::create(['title' => 'Cũ', 'price' => 1, 'cover_image_url' => $cover]);

        $this->put(route('books.update', $book), ['title' => 'Mới', 'price' => 2])->assertSessionHasNoErrors();

        $this->assertSame($cover, $book->refresh()->cover_image_url);
        Storage::disk('public')->assertExists($cover);
    }

    public function test_destroy_deletes_book_pivots_and_cover(): void
    {
        $cover = UploadedFile::fake()->image('c.jpg')->store('books', 'public');
        $book = Book::create(['title' => 'Xóa', 'price' => 1, 'cover_image_url' => $cover]);
        $book->categories()->attach(Category::create(['name' => 'X']));

        $this->delete(route('books.destroy', $book))
            ->assertRedirect(route('books.index'))
            ->assertSessionHas('success');

        $this->assertModelMissing($book);
        $this->assertDatabaseMissing('book_category', ['book_id' => $book->id]);
        Storage::disk('public')->assertMissing($cover);
    }

    public function test_read_pages_render(): void
    {
        $book = Book::create(['title' => 'Xem', 'price' => 1]);

        $this->get(route('books.index'))->assertOk()->assertSee('Xem');
        $this->get(route('books.create'))->assertOk();
        $this->get(route('books.show', $book))->assertOk()->assertViewHas('relatedBooks');
        $this->get(route('books.edit', $book))->assertOk();
    }
}
