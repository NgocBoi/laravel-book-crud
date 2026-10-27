<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookPermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_must_log_in_before_accessing_books(): void
    {
        $book = Book::create(['title' => 'Sách', 'price' => 1]);

        $this->get(route('books.index'))->assertRedirect(route('login'));
        $this->get(route('books.show', $book))->assertRedirect(route('login'));
        $this->get(route('books.create'))->assertRedirect(route('login'));
        $this->get(route('books.edit', $book))->assertRedirect(route('login'));
        $this->post(route('books.store'))->assertRedirect(route('login'));
        $this->put(route('books.update', $book))->assertRedirect(route('login'));
        $this->delete(route('books.destroy', $book))->assertRedirect(route('login'));
        $this->get(route('books.trash'))->assertRedirect(route('login'));
    }

    public function test_admin_can_access_book_management_pages(): void
    {
        $book = Book::create(['title' => 'Sách quản trị', 'price' => 1]);
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('books.index'))->assertOk()->assertSee('Thêm sách')->assertSee('aria-label="Sửa sách', false);
        $this->get(route('books.create'))->assertOk();
        $this->get(route('books.edit', $book))->assertOk();
        $this->get(route('books.show', $book))->assertOk()->assertSee('Chỉnh sửa');
    }

    public function test_regular_user_can_read_books_but_cannot_manage_them(): void
    {
        $book = Book::create(['title' => 'Sách chỉ đọc', 'price' => 1]);
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->get(route('books.index'))
            ->assertOk()
            ->assertSee('Sách chỉ đọc')
            ->assertDontSee('aria-label="Sửa sách', false)
            ->assertDontSee('Thêm sách');
        $this->get(route('books.show', $book))->assertOk()->assertDontSee('Chỉnh sửa');
        $this->get(route('books.create'))->assertForbidden();
        $this->post(route('books.store'))->assertForbidden();
        $this->get(route('books.edit', $book))->assertForbidden();
        $this->put(route('books.update', $book))->assertForbidden();
        $this->delete(route('books.destroy', $book))->assertForbidden();
        $this->get(route('books.trash'))->assertForbidden();
        $this->get(route('books.exportExcel'))->assertForbidden();

        $this->assertDatabaseHas('books', ['id' => $book->id, 'title' => 'Sách chỉ đọc', 'deleted_at' => null]);
    }

    public function test_login_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'secret123', 'role' => 'admin']);

        $this->get(route('login'))->assertOk();
        $this->post(route('login'), ['email' => 'admin@example.com', 'password' => 'sai-mat-khau'])
            ->assertSessionHasErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        $this->assertGuest();

        $this->post(route('login'), ['email' => 'admin@example.com', 'password' => 'secret123'])
            ->assertRedirect(route('books.index'));
        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('logout_success', 'Bạn đã đăng xuất.');
        $this->assertGuest();

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('logout-toast')
            ->assertSee('Bạn đã đăng xuất.');
    }
}
