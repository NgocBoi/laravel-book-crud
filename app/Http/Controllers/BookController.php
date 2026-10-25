<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        return view('books.create', $this->formOptions());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request)
    {
        $data = $this->bookAttributes($request);
        $newCover = $this->storeCover($request);

        if ($newCover) {
            $data['cover_image_url'] = $newCover;
        }

        try {
            $book = DB::transaction(function () use ($request, $data) {
                $book = Book::create($data);
                $this->syncRelations($book, $request);

                return $book;
            });
        } catch (Throwable $e) {
            $this->deleteCover($newCover);

            throw $e;
        }

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'Đã thêm sách "'.$book->title.'".');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load(['publisher', 'authors', 'categories', 'warehouses']);

        $relatedBooks = Book::with('authors')
            ->whereKeyNot($book->id)
            ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $book->categories->modelKeys()))
            ->latest('id')
            ->limit(4)
            ->get();

        return view('books.show', compact('book', 'relatedBooks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $book->load(['authors', 'categories', 'warehouses']);

        return view('books.edit', ['book' => $book, ...$this->formOptions()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookRequest $request, Book $book)
    {
        $data = $this->bookAttributes($request);
        $oldCover = $book->cover_image_url;
        $newCover = $this->storeCover($request);

        if ($newCover) {
            $data['cover_image_url'] = $newCover;
        }

        try {
            DB::transaction(function () use ($request, $book, $data) {
                $book->update($data);
                $this->syncRelations($book, $request);
            });
        } catch (Throwable $e) {
            $this->deleteCover($newCover);

            throw $e;
        }

        // Chỉ xóa ảnh cũ sau khi đã lưu thành công ảnh mới vào CSDL
        if ($newCover) {
            $this->deleteCover($oldCover);
        }

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'Đã cập nhật sách "'.$book->title.'".');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        // Các dòng trong author_book, book_category, book_warehouse tự xóa theo cascadeOnDelete
        DB::transaction(fn () => $book->delete());

        // Khi Model bật SoftDeletes, giữ lại ảnh bìa để còn khôi phục từ thùng rác
        if (! in_array(SoftDeletes::class, class_uses_recursive($book))) {
            $this->deleteCover($book->cover_image_url);
        }

        return redirect()
            ->route('books.index')
            ->with('success', 'Đã xóa sách "'.$book->title.'".');
    }

    /**
     * Danh sách thể loại, tác giả, NXB cho form thêm / sửa.
     */
    private function formOptions(): array
    {
        return [
            'categories' => Category::orderBy('name')->get(),
            'authors' => Author::orderBy('name')->get(),
            'publishers' => Publisher::orderBy('name')->get(),
        ];
    }

    /**
     * Các cột của bảng books lấy từ dữ liệu đã validate (bỏ quan hệ và file ảnh).
     */
    private function bookAttributes(FormRequest $request): array
    {
        return $request->safe()->except(['author_ids', 'category_ids', 'cover_image']);
    }

    /**
     * Đồng bộ bảng trung gian tác giả / thể loại theo lựa chọn trên form.
     */
    private function syncRelations(Book $book, FormRequest $request): void
    {
        $book->authors()->sync($request->validated('author_ids', []));
        $book->categories()->sync($request->validated('category_ids', []));
    }

    /**
     * Lưu ảnh bìa mới (nếu có) và trả về đường dẫn tương đối trên disk "public".
     */
    private function storeCover(FormRequest $request): ?string
    {
        if (! $request->hasFile('cover_image')) {
            return null;
        }

        return $request->file('cover_image')->store(self::COVER_DIRECTORY, 'public') ?: null;
    }

    /**
     * Xóa ảnh bìa trong storage; bỏ qua ảnh là URL ngoài.
     */
    private function deleteCover(?string $path): void
    {
        if (! $path || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        Storage::disk('public')->delete($path);
    }
}
