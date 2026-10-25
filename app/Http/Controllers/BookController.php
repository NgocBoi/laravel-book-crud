<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $books = Book::with(['publisher', 'authors', 'categories', 'warehouses'])
            ->latest('id')
            ->paginate(10);

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('books.create', [
            'categories' => Category::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
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
    public function show(Book $book)
    {
        $book->load(['publisher', 'authors', 'categories', 'warehouses']);

        return view('books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $book->load(['authors', 'categories', 'warehouses']);

        return view('books.edit', [
            'book' => $book,
            'categories' => Category::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ]);
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
