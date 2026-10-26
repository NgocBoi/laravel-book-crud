<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class BookTrashController extends Controller
{
    /**
     * Danh sách sách đã xóa mềm, mới xóa xếp trước.
     */
    public function index(): View
    {
        $books = Book::onlyTrashed()
            ->with(['authors', 'categories', 'publisher'])
            ->latest('deleted_at')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('books.trash', compact('books'));
    }

    /**
     * Khôi phục sách từ thùng rác về danh sách.
     */
    public function restore(int $id): RedirectResponse
    {
        $book = Book::onlyTrashed()->findOrFail($id);
        $book->restore();

        return to_route('books.trash')->with('success', "Đã khôi phục sách \"{$book->title}\".");
    }

    /**
     * Xóa vĩnh viễn sách đã ở thùng rác, kèm liên kết tác giả / thể loại và file ảnh bìa.
     */
    public function forceDelete(int $id): RedirectResponse
    {
        $book = Book::onlyTrashed()->findOrFail($id);

        DB::transaction(function () use ($book): void {
            $book->authors()->detach();
            $book->categories()->detach();
            $book->forceDelete();
        });

        $book->deleteCoverFile();

        return to_route('books.trash')->with('success', "Đã xóa vĩnh viễn sách \"{$book->title}\".");
    }
}
