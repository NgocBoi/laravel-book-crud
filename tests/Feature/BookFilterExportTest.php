<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ZipArchive;

class BookFilterExportTest extends TestCase
{
    use RefreshDatabase;

    private Category $novel;

    private Category $science;

    protected function setUp(): void
    {
        parent::setUp();

        $this->novel = Category::create(['name' => 'Tiểu thuyết']);
        $this->science = Category::create(['name' => 'Khoa học']);

        $this->makeBook('Nhà Giả Kim', '9786041111111', 'Paulo Coelho', $this->novel, 20);
        $this->makeBook('Lược Sử Thời Gian', '9786042222222', 'Stephen Hawking', $this->science, 3);
        $this->makeBook('Vũ Trụ', '9786043333333', 'Carl Sagan', $this->science, 0);
    }

    public function test_keyword_searches_title_isbn_and_author(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->assertListed(['keyword' => 'giả kim'], ['Nhà Giả Kim']);
        $this->assertListed(['keyword' => '978-604-222'], ['Lược Sử Thời Gian']);
        $this->assertListed(['keyword' => 'Sagan'], ['Vũ Trụ']);
    }

    public function test_category_and_stock_filters_can_be_combined(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->assertListed(['category_id' => $this->science->id], ['Lược Sử Thời Gian', 'Vũ Trụ']);
        $this->assertListed(['stock' => 'in'], ['Nhà Giả Kim']);
        $this->assertListed(['stock' => 'low'], ['Lược Sử Thời Gian']);
        $this->assertListed(['stock' => 'out'], ['Vũ Trụ']);
        $this->assertListed(['category_id' => $this->science->id, 'stock' => 'out'], ['Vũ Trụ']);
    }

    public function test_invalid_filter_values_are_ignored(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->assertListed(['category_id' => 'abc', 'stock' => 'khac', 'keyword' => ['x']], ['Nhà Giả Kim', 'Lược Sử Thời Gian', 'Vũ Trụ']);
    }

    public function test_index_offers_category_options_and_export_link_with_current_filters(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('books.index', ['stock' => 'low']))
            ->assertOk()
            ->assertSee('<option value="'.$this->science->id.'"', false)
            ->assertSee(e(route('books.exportExcel', ['stock' => 'low'])), false);
    }

    public function test_admin_exports_filtered_books_as_xlsx(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->get(route('books.exportExcel', ['category_id' => $this->science->id]))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition'));

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($response->getFile()->getPathname()));
        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        $this->assertNotFalse(simplexml_load_string($sheet));
        $this->assertStringContainsString('Tên sách', $sheet);
        $this->assertStringContainsString('Lược Sử Thời Gian', $sheet);
        $this->assertStringContainsString('Vũ Trụ', $sheet);
        $this->assertStringContainsString('Hết hàng', $sheet);
        $this->assertStringNotContainsString('Nhà Giả Kim', $sheet);
    }

    public function test_trash_and_export_buttons_are_shown_only_to_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('books.index'))
            ->assertSee('href="'.route('books.trash').'"', false)
            ->assertSee('Xuất Excel')
            ->assertSee('Hệ thống quản lý sách');

        $this->actingAs(User::factory()->create(['role' => 'user']));
        $this->get(route('books.index'))
            ->assertDontSee('href="'.route('books.trash').'"', false)
            ->assertDontSee('Xuất Excel')
            ->assertSee('Hệ thống quản lý sách');
    }

    /**
     * @param  array<string, mixed>  $query
     * @param  list<string>  $expectedTitles
     */
    private function assertListed(array $query, array $expectedTitles): void
    {
        $titles = $this->get(route('books.index', $query))
            ->assertOk()
            ->viewData('books')
            ->pluck('title')
            ->sort()
            ->values()
            ->all();

        $this->assertSame(collect($expectedTitles)->sort()->values()->all(), $titles);
    }

    private function makeBook(string $title, string $isbn, string $author, Category $category, int $stock): void
    {
        $book = Book::create(['title' => $title, 'isbn' => $isbn, 'price' => 100000, 'stock_quantity' => $stock]);
        $book->authors()->attach(Author::create(['name' => $author]));
        $book->categories()->attach($category);
    }
}
