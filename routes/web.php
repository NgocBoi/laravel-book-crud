<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\BookStockController;
use App\Http\Controllers\BookTrashController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Phân quyền đăng nhập: tất cả route bên trong yêu cầu người dùng đã đăng nhập.
Route::middleware('auth')->group(function () {
    // Chỉ admin được xem thùng rác, khôi phục hoặc xóa sách vĩnh viễn.
    Route::prefix('books/trash')->middleware('admin')->group(function () {
        Route::get('/', [BookTrashController::class, 'index'])->name('books.trash');
        Route::post('/{id}/restore', [BookTrashController::class, 'restore'])->name('books.restore');
        Route::delete('/{id}/force-delete', [BookTrashController::class, 'forceDelete'])->name('books.forceDelete');
    });

    // Chỉ admin được nhập thêm số lượng tồn kho.
    Route::post('/books/{id}/stock-in', [BookStockController::class, 'stockIn'])
        ->name('books.stockIn')
        ->middleware('admin');

    // Chỉ admin được xuất dữ liệu.
    Route::get('/books/export/excel', [BookExportController::class, 'exportExcel'])
        ->name('books.exportExcel')
        ->middleware('admin');

    // User đã đăng nhập được xem danh sách/chi tiết; chỉ admin được thêm, sửa, xóa.
    Route::resource('books', BookController::class)
        ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], 'admin');
});
