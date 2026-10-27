<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookTrashTest extends TestCase
{
    use RefreshDatabase;

    public function test_trash_lists_only_soft_deleted_books(): void
    {
        Book::create(['title' => 'Sách còn hoạt động', 'price' => 1]);
        $deleted = Book::create(['title' => 'Sách đã xóa', 'price' => 1]);
        $deleted->delete();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('books.trash'))
            ->assertOk()
            ->assertSee('Sách đã xóa')
            ->assertDontSee('Sách còn hoạt động');
    }

    public function test_admin_can_restore_a_deleted_book(): void
    {
        $book = Book::create(['title' => 'Sách khôi phục', 'price' => 1]);
        $book->delete();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('books.restore', $book->id))->assertRedirect(route('books.trash'));

        $this->assertNotSoftDeleted('books', ['id' => $book->id]);
    }

    public function test_admin_can_force_delete_a_book_with_its_relations_and_cover(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('covers/cover.jpg', 'x');
        $book = Book::create(['title' => 'Sách xóa hẳn', 'price' => 1, 'cover_image_url' => 'covers/cover.jpg']);
        $book->authors()->attach(Author::create(['name' => 'Tác giả']));
        $book->delete();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->delete(route('books.forceDelete', $book->id))->assertRedirect(route('books.trash'));

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('author_book', ['book_id' => $book->id]);
        Storage::disk('public')->assertMissing('covers/cover.jpg');
    }

    public function test_active_book_cannot_be_restored_or_force_deleted_through_trash(): void
    {
        $book = Book::create(['title' => 'Sách đang hoạt động', 'price' => 1]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('books.restore', $book->id))->assertNotFound();
        $this->delete(route('books.forceDelete', $book->id))->assertNotFound();

        $this->assertDatabaseHas('books', ['id' => $book->id]);
    }

    public function test_normal_user_cannot_use_trash_actions(): void
    {
        $book = Book::create(['title' => 'Sách', 'price' => 1]);
        $book->delete();
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->post(route('books.restore', $book->id))->assertForbidden();
        $this->delete(route('books.forceDelete', $book->id))->assertForbidden();

        $this->assertSoftDeleted('books', ['id' => $book->id]);
    }
}
