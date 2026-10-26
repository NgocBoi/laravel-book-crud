<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $books = Book::with(['authors', 'categories', 'publisher'])
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->string('keyword')->trim();

                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'like', "%{$keyword}%")
                        ->orWhere('isbn', 'like', "%{$keyword}%")
                        ->orWhereHas('authors', fn ($a) => $a->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($request->filled('category_id'), fn ($query) => $query->whereHas(
                'categories',
                fn ($c) => $c->where('categories.id', $request->input('category_id'))
            ))
            ->when($request->input('stock') === 'in', fn ($query) => $query->where('stock_quantity', '>=', Book::LOW_STOCK))
            ->when($request->input('stock') === 'low', fn ($query) => $query->whereBetween('stock_quantity', [1, Book::LOW_STOCK - 1]))
            ->when($request->input('stock') === 'out', fn ($query) => $query->where('stock_quantity', 0))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('books.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('books.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $book = DB::transaction(function () use ($data): Book {
            $book = Book::create(Arr::except($data, ['author_ids', 'category_ids']));
            $book->authors()->sync($data['author_ids']);
            $book->categories()->sync($data['category_ids']);

            return $book;
        });

        return to_route('books.edit', $book)->with('success', 'Đã thêm sách mới thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with(['authors', 'categories', 'publisher'])->findOrFail($id);

        $related = Book::with('authors')
            ->whereKeyNot($book->id)
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $book->categories->modelKeys()))
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'related'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book): View
    {
        return view('books.edit', [
            ...$this->formOptions(),
            'book' => $book->load(['authors', 'categories', 'publisher']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $data = $request->validated();
        DB::transaction(function () use ($book, $data): void {
            $book->update(Arr::except($data, ['author_ids', 'category_ids']));
            $book->authors()->sync($data['author_ids']);
            $book->categories()->sync($data['category_ids']);
        });

        return to_route('books.edit', $book)->with('success', 'Đã cập nhật sách thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * @return array{
     *     authors: Collection<int, Author>,
     *     categories: Collection<int, Category>,
     *     publishers: Collection<int, Publisher>
     * }
     */
    private function formOptions(): array
    {
        return [
            'authors' => Author::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ];
    }
}
