<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return redirect()->route('books.index');
});

// Sách: CRUD đầy đủ
Route::resource('books', BookController::class);
