<?php

namespace Tests\Feature;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_index_filters_by_category_author_publisher_and_price_range(): void
    {
        $category = Category::create(['name' => 'Văn học']);
        $author = Author::create(['name' => 'Tác giả phù hợp']);
        $publisher = Publisher::create(['name' => 'NXB phù hợp']);
        $matchingBook = $this->createBook('Sách phù hợp', 100, $publisher);
        $matchingBook->categories()->attach($category);
        $matchingBook->authors()->attach($author);

        $otherBook = $this->createBook('Sách khác', 150, $publisher);
        $otherBook->categories()->attach($category);
        $otherBook->authors()->attach($author);

        $this->get(route('books.index', [
            'category_id' => $category->id,
            'author_id' => $author->id,
            'publisher_id' => $publisher->id,
            'min_price' => 90,
            'max_price' => 120,
        ]))
            ->assertOk()
            ->assertSee($matchingBook->title)
            ->assertDontSee($otherBook->title);
    }

    public function test_index_can_filter_by_a_zero_maximum_price(): void
    {
        $freeBook = $this->createBook('Sách miễn phí', 0);
        $paidBook = $this->createBook('Sách có giá', 1);

        $this->get(route('books.index', ['max_price' => 0]))
            ->assertOk()
            ->assertSee($freeBook->title)
            ->assertDontSee($paidBook->title);
    }

    public function test_index_sorts_by_price_and_creation_time(): void
    {
        $olderBook = $this->createBook('Sách tạo trước', 100, null, now()->subDays(2)->toDateTimeString());
        $newerBook = $this->createBook('Sách tạo sau', 100, null, now()->subDay()->toDateTimeString());
        $cheapBook = $this->createBook('Sách giá thấp', 50);
        $expensiveBook = $this->createBook('Sách giá cao', 150);

        $this->get(route('books.index', ['sort' => 'price_asc']))
            ->assertOk()
            ->assertViewHas('books', function ($books) use ($cheapBook, $olderBook, $newerBook, $expensiveBook): bool {
                return $books->pluck('id')->all() === [$cheapBook->id, $newerBook->id, $olderBook->id, $expensiveBook->id];
            });

        $this->get(route('books.index', ['sort' => 'price_desc']))
            ->assertOk()
            ->assertViewHas('books', function ($books) use ($cheapBook, $olderBook, $newerBook, $expensiveBook): bool {
                return $books->pluck('id')->all() === [$expensiveBook->id, $newerBook->id, $olderBook->id, $cheapBook->id];
            });

        $this->get(route('books.index', ['sort' => 'oldest']))
            ->assertOk()
            ->assertViewHas('books', function ($books) use ($olderBook, $newerBook, $cheapBook, $expensiveBook): bool {
                return $books->pluck('id')->all() === [$olderBook->id, $newerBook->id, $cheapBook->id, $expensiveBook->id];
            });

        $this->get(route('books.index'))
            ->assertOk()
            ->assertViewHas('books', function ($books) use ($olderBook, $newerBook, $cheapBook, $expensiveBook): bool {
                return $books->pluck('id')->all() === [$expensiveBook->id, $cheapBook->id, $newerBook->id, $olderBook->id];
            });
    }

    public function test_pagination_keeps_active_filters_in_page_links(): void
    {
        $category = Category::create(['name' => 'Khoa học']);

        foreach (range(1, 11) as $number) {
            $book = $this->createBook("Sách {$number}", 100);
            $book->categories()->attach($category);
        }

        $response = $this->get(route('books.index', [
            'category_id' => $category->id,
            'min_price' => 50,
            'sort' => 'price_asc',
            'page' => 2,
        ]));

        $response->assertOk()
            ->assertViewHas('books', function ($books) use ($category): bool {
                return $books->currentPage() === 2
                    && $books->url(1) === route('books.index', [
                        'category_id' => $category->id,
                        'min_price' => 50,
                        'sort' => 'price_asc',
                        'page' => 1,
                    ]);
            });
    }

    private function createBook(string $title, int $price, ?Publisher $publisher = null, ?string $createdAt = null): Book
    {
        $book = Book::create([
            'title' => $title,
            'price' => $price,
            'publisher_id' => $publisher?->id,
        ]);

        if ($createdAt !== null) {
            $book->forceFill(['created_at' => $createdAt])->save();
        }

        return $book;
    }
}
