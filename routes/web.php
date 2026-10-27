<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookTrashController;
// use App\Http\Controllers\BookStockController;
use App\Http\Controllers\BookExportController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('books.index');
});

// Router của đăng nhập với đăng xuất

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

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
