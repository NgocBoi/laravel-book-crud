<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
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

    // Chỉ Admin được Thêm / Sửa / Xóa; khách phải đăng nhập mới xem được
    Route::resource('books', BookController::class)
        ->middlewareFor(['index', 'show'], ['auth'])
        ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], ['auth', 'admin']);
});
