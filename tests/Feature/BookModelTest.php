<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BookModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_schema_uses_stock_quantity_and_soft_deletes(): void
    {
        $this->assertTrue(Schema::hasColumns('books', ['stock_quantity', 'deleted_at']));
        $this->assertFalse(Schema::hasTable('warehouses'));
        $this->assertFalse(Schema::hasTable('book_warehouse'));
    }

    public function test_seeder_creates_books_with_relations_and_stock(): void
    {
        $this->seed();

        $this->assertSame(58, Book::count());
        $this->assertTrue(Book::where('stock_quantity', 0)->exists());
        $this->assertTrue(Book::where('stock_quantity', '>=', Book::LOW_STOCK)->exists());
        $this->assertNotEmpty(Book::first()->authors);
        $this->assertNotEmpty(Book::first()->categories);
    }

    public function test_soft_delete_keeps_row(): void
    {
        $book = Book::create(['title' => 'Sách thử', 'price' => 1000]);

        $book->delete();

        $this->assertSoftDeleted($book);
        $this->assertSame(0, Book::count());
        $this->assertSame(1, Book::withTrashed()->count());
    }
}
