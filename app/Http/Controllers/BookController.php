<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Services\BookFilterService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BookController extends Controller
{
    /**
     * Thư mục lưu ảnh bìa trên disk "public" (storage/app/public/books).
     */
    private const COVER_DIRECTORY = 'books';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, BookFilterService $bookFilterService): View
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'author_id' => ['nullable', 'integer', 'exists:authors,id'],
            'publisher_id' => ['nullable', 'integer', 'exists:publishers,id'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'in:in,low,out'],
            'sort' => ['nullable', 'in:newest,oldest,price_asc,price_desc'],
        ]);

        return view('books.index', [
            'books' => $bookFilterService->paginate($filters),
            'authors' => Author::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
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
        try {
            $book = $this->persist(new Book, $request);
        } catch (QueryException $e) {
            return $this->saveFailed($e);
        }

        return to_route('books.edit', $book)->with('success', 'Đã thêm sách mới thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book): View
    {
        $book->load(['authors', 'categories', 'publisher']);

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
        try {
            $this->persist($book, $request);
        } catch (QueryException $e) {
            return $this->saveFailed($e);
        }

        return to_route('books.index')->with('success', 'Đã cập nhật sách thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book): RedirectResponse
    {
        // Xóa mềm: chỉ ghi deleted_at, giữ bản ghi, liên kết tác giả/thể loại và ảnh bìa để còn khôi phục
        $book->delete();

        return to_route('books.index')->with('success', "Đã chuyển sách \"{$book->title}\" vào thùng rác.");
    }

    /**
     * Lưu sách (thêm mới hoặc cập nhật) cùng quan hệ tác giả / thể loại trong một transaction:
     * hoặc lưu đủ tất cả, hoặc không lưu gì.
     * Có chọn ảnh bìa mới thì lưu ảnh mới, lưu xong mới xóa ảnh cũ; lưu CSDL lỗi thì xóa ảnh vừa upload.
     */
    private function persist(Book $book, StoreBookRequest $request): Book
    {
        $previous = clone $book;
        $newCover = $request->storeCover();

        try {
            DB::transaction(function () use ($book, $request, $newCover): void {
                $book->fill($request->bookData());

                if ($newCover) {
                    $book->cover_image_url = $newCover;
                }

                $book->save();
                $book->authors()->sync($request->validated('author_ids'));
                $book->categories()->sync($request->validated('category_ids'));
            });
        } catch (Throwable $e) {
            if ($newCover) {
                Storage::disk('public')->delete($newCover);
            }

            throw $e;
        }

        if ($newCover) {
            $previous->deleteCoverFile();
        }

        return $book;
    }

    /**
     * Lỗi CSDL khi lưu (trùng khóa, mất kết nối...): ghi log, giữ nguyên dữ liệu đã nhập và báo người dùng.
     */
    private function saveFailed(QueryException $e): RedirectResponse
    {
        report($e);

        return back()->withInput()->with('error', 'Không thể lưu sách do lỗi hệ thống. Vui lòng kiểm tra lại dữ liệu và thử lại.');
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
