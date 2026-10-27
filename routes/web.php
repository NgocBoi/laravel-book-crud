<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
<<<<<<< HEAD
use App\Http\Controllers\BookTrashController;
// use App\Http\Controllers\BookStockController;
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\AuthController;
=======
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\BookStockController;
use App\Http\Controllers\BookTrashController;
use Illuminate\Support\Facades\Route;
>>>>>>> bc43ff25f6212ba1379be7c1b92bc4b7d7b29867

Route::get('/', function () {
    return redirect()->route('books.index');
});

<<<<<<< HEAD
// Router của đăng nhập với đăng xuất

=======
>>>>>>> bc43ff25f6212ba1379be7c1b92bc4b7d7b29867
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

<<<<<<< HEAD
// =============================================================================
// PHÂN CÔNG ROUTE CHO CÁC THÀNH VIÊN
// =============================================================================

// TV6: Thùng rác & Khôi phục (Đặt trước resource để tránh xung đột id)
// 
// Route::prefix('books/trash')->group(function () {
//     Route::get('/', [BookTrashController::class, 'index'])->name('books.trash');
//     Route::post('/{id}/restore', [BookTrashController::class, 'restore'])->name('books.restore');
//     Route::delete('/{id}/force-delete', [BookTrashController::class, 'forceDelete'])->name('books.forceDelete');
// });
// Thay cho phần đoạn code trên phục vụ phần phân quyền
Route::prefix('books/trash')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [BookTrashController::class, 'index'])->name('books.trash');
    Route::post('/{id}/restore', [BookTrashController::class, 'restore'])->name('books.restore');
    Route::delete('/{id}/force-delete', [BookTrashController::class, 'forceDelete'])->name('books.forceDelete');
});

// TV7: Nhập thêm số lượng tồn kho
// Route::post('/books/{id}/stock-in', [BookStockController::class, 'stockIn'])->name('books.stockIn');
// Route::post('/books/{id}/stock-in', [BookStockController::class, 'stockIn'])
//     ->name('books.stockIn')
//     ->middleware(['auth', 'admin']);
    
// TV8: Xuất dữ liệu Excel/PDF
Route::get('/books/export/excel', [BookExportController::class, 'exportExcel'])->name('books.exportExcel');

// TV2, TV3, TV4, TV5: CRUD Chuẩn Resource
// Route::resource('books', BookController::class);
// Chỉ Admin được Thêm / Sửa / Xóa 
Route::resource('books', BookController::class)
    ->middlewareFor(['index', 'show'], ['auth'])
    ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], ['auth', 'admin']);
// Sách: CRUD đầy đủ
// Route::resource('books', BookController::class);
// Sách: CRUD đầy đủ
Route::resource('books', BookController::class);
=======
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
>>>>>>> bc43ff25f6212ba1379be7c1b92bc4b7d7b29867
