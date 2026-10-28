<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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

    public function test_price_range_filter_is_inclusive_and_can_be_open_ended(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));
        Book::firstWhere('title', 'Vũ Trụ')->update(['price' => 250000]);
        Book::firstWhere('title', 'Lược Sử Thời Gian')->update(['price' => 150000]);

        $this->assertListed(['price_min' => 150000], ['Lược Sử Thời Gian', 'Vũ Trụ']);
        $this->assertListed(['price_max' => 150000], ['Nhà Giả Kim', 'Lược Sử Thời Gian']);
        $this->assertListed(['price_min' => 120000, 'price_max' => 200000], ['Lược Sử Thời Gian']);
        $this->assertListed(['price_min' => 0, 'price_max' => 0], []);
    }

    public function test_books_can_be_sorted_by_price(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));
        Book::firstWhere('title', 'Nhà Giả Kim')->update(['price' => 100000]);
        Book::firstWhere('title', 'Lược Sử Thời Gian')->update(['price' => 150000]);
        Book::firstWhere('title', 'Vũ Trụ')->update(['price' => 250000]);

        $titlesFor = fn (string $sort): array => $this->get(route('books.index', ['sort' => $sort]))
            ->assertOk()
            ->viewData('books')
            ->pluck('title')
            ->all();

        $this->assertSame(['Nhà Giả Kim', 'Lược Sử Thời Gian', 'Vũ Trụ'], $titlesFor('price_asc'));
        $this->assertSame(['Vũ Trụ', 'Lược Sử Thời Gian', 'Nhà Giả Kim'], $titlesFor('price_desc'));
    }

    public function test_invalid_filter_values_are_ignored(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));

        $this->assertListed(['price_min' => 'abc', 'price_max' => '-5', 'category_id' => 'abc', 'stock' => 'khac', 'keyword' => ['x']], ['Nhà Giả Kim', 'Lược Sử Thời Gian', 'Vũ Trụ']);
    }

    public function test_index_offers_category_options(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->get(route('books.index', ['stock' => 'low']))
            ->assertOk()
            ->assertSee('<option value="'.$this->science->id.'"', false);
    }

    public function test_detail_page_shows_categories_in_its_own_box(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'user']));
        $book = Book::firstWhere('title', 'Vũ Trụ');
        $book->categories()->attach($this->novel);

        $this->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeInOrder(['<dt>Thể loại</dt>', 'Khoa học', 'Nhà xuất bản'], false)
            ->assertSeeInOrder(['<dt>Thể loại</dt>', 'Tiểu thuyết', 'Nhà xuất bản'], false);
    }

    public function test_trash_button_is_shown_only_to_admin(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        $this->get(route('books.index'))
            ->assertSee('href="'.route('books.trash').'"', false)
            ->assertSee('Hệ thống quản lý sách');

        $this->actingAs(User::factory()->create(['role' => 'user']));
        $this->get(route('books.index'))
            ->assertDontSee('href="'.route('books.trash').'"', false)
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
