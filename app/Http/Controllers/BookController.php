<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
