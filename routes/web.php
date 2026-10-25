<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookTrashController;
use App\Http\Controllers\BookStockController;
use App\Http\Controllers\BookExportController;

Route::get('/', function () {
    return redirect()->route('books.index');
});

// =============================================================================
// PHÂN CÔNG ROUTE CHO CÁC THÀNH VIÊN
// =============================================================================

// TV6: Thùng rác & Khôi phục (Đặt trước resource để tránh xung đột id)
Route::prefix('books/trash')->group(function () {
    Route::get('/', [BookTrashController::class, 'index'])->name('books.trash');
    Route::post('/{id}/restore', [BookTrashController::class, 'restore'])->name('books.restore');
    Route::delete('/{id}/force-delete', [BookTrashController::class, 'forceDelete'])->name('books.forceDelete');
});

// TV7: Nhập thêm số lượng tồn kho
Route::post('/books/{id}/stock-in', [BookStockController::class, 'stockIn'])->name('books.stockIn');

// TV8: Xuất dữ liệu Excel/PDF
Route::get('/books/export/excel', [BookExportController::class, 'exportExcel'])->name('books.exportExcel');

// TV2, TV3, TV4, TV5: CRUD Chuẩn Resource
Route::resource('books', BookController::class);
