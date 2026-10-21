# ==============================================================================
# SCRIPT TỰ ĐỘNG KHỞI TẠO BỘ KHUNG QUẢN LÝ SÁCH CHO NHÓM 8 THÀNH VIÊN
# Chạy script này tại thư mục gốc của dự án Laravel
# ==============================================================================

# Cấu hình PHP 8.4: Ưu tiên C:\xampp\php_new\php.exe
$phpBinary = "php"
if (Test-Path "C:\xampp\php_new\php.exe") {
    $phpBinary = "C:\xampp\php_new\php.exe"
    $env:PATH = "C:\xampp\php_new;" + $env:PATH
    Write-Host "[INFO] Đang sử dụng PHP 8.4 tại: $phpBinary" -ForegroundColor Cyan
} else {
    Write-Host "[WARN] Không tìm thấy C:\xampp\php_new\php.exe, sử dụng php mặc định." -ForegroundColor Yellow
}

Write-Host ">>> BẮT ĐẦU KHỞI TẠO BỘ KHUNG DỰ ÁN CHO 8 THÀNH VIÊN..." -ForegroundColor Cyan

# 1. TẠO CONTROLLERS BẰNG ARTISAN (Chuẩn hóa Namespace và Class)
Write-Host "1. Đang tạo các Controllers phân công..." -ForegroundColor Yellow
& $phpBinary artisan make:controller BookController --resource
& $phpBinary artisan make:controller BookTrashController
& $phpBinary artisan make:controller BookStockController
& $phpBinary artisan make:controller BookExportController

# 2. TẠO REQUESTS VALIDATION (TV3, TV5, TV7)
Write-Host "2. Đang tạo các Form Request Validation..." -ForegroundColor Yellow
& $phpBinary artisan make:request StoreBookRequest
& $phpBinary artisan make:request UpdateBookRequest
& $phpBinary artisan make:request UpdateStockRequest

# 3. TẠO POLICY BẢO MẬT & PHÂN QUYỀN (TV8)
Write-Host "3. Đang tạo Policy phân quyền..." -ForegroundColor Yellow
& $phpBinary artisan make:policy BookPolicy --model=Book

# 4. TẠO CÁC THƯ MỤC VÀ FILE BLADE VIEWS
Write-Host "4. Đang tạo cây thư mục và file giao diện Blade Views..." -ForegroundColor Yellow
$viewDirs = @(
    "resources/views/layouts",
    "resources/views/books",
    "resources/views/books/partials"
)

foreach ($dir in $viewDirs) {
    if (-not (Test-Path -Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}

# 5. KHỞI TẠO CÁC FILE VIEW VỚI COMMENT GẮN NHÃN PHÂN CÔNG RÕ RÀNG
$filesContent = @{
    # LAYOUT CHUNG
    "resources/views/layouts/app.blade.php"                     = @'
{{-- 
=============================================================================
LAYOUT DÙNG CHUNG TOÀN HỆ THỐNG (Bootstrap 5 + Icons)
Phụ trách khởi tạo: Trưởng nhóm (TV1)
=============================================================================
--}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý Sách')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
'@;

    # TV2: DANH SÁCH & BỘ LỌC
    "resources/views/books/index.blade.php"                     = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 2
NHIỆM VỤ: Hiển thị bảng danh sách sách, phân trang, kết nối các modal
=============================================================================
--}}
@extends('layouts.app')
@section('title', 'Danh mục Sách')

@section('content')
<div class="container py-4">
    <h3 class="fw-bold text-primary mb-3">QUẢN LÝ ĐẦU SÁCH</h3>
    
    {{-- TV2 nhúng bộ lọc --}}
    @include('books.partials.filter-bar')

    {{-- TV8 nhúng nút Export --}}
    @include('books.partials.action-buttons')

    {{-- Bảng dữ liệu chính ở đây --}}
</div>

{{-- Nhúng các Modal do TV4 và TV7 phụ trách --}}
@include('books.partials.quick-view-modal')
@include('books.partials.stock-modal')
@endsection
'@;

    "resources/views/books/partials/filter-bar.blade.php"       = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 2
NHIỆM VỤ: Form tìm kiếm từ khóa, lọc theo loại sách, lọc theo khoảng giá
=============================================================================
--}}
<div class="card p-3 mb-3 border-0 shadow-sm">
    <p class="text-muted small mb-0">[TV2 Code form tìm kiếm và lọc tại đây]</p>
</div>
'@;

    # TV3: FORM THÊM MỚI & PREVIEW ẢNH
    "resources/views/books/create.blade.php"                    = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 3
NHIỆM VỤ: Form nhập liệu thêm mới đầu sách, bắt lỗi Validate @error
=============================================================================
--}}
@extends('layouts.app')
@section('title', 'Thêm mới Đầu sách')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success text-white"><h5>THÊM MỚI ĐẦU SÁCH</h5></div>
        <div class="card-body">
            {{-- TV3 code form thêm mới sách tại đây --}}
        </div>
    </div>
</div>
@endsection
'@;

    # TV4: CHI TIẾT SÁCH & QUICK VIEW MODAL
    "resources/views/books/show.blade.php"                      = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 4
NHIỆM VỤ: Trang chi tiết đầu sách + danh sách sách cùng thể loại gợi ý
=============================================================================
--}}
@extends('layouts.app')
@section('title', 'Chi tiết Đầu sách')

@section('content')
<div class="container py-4">
    {{-- TV4 code giao diện chi tiết sách tại đây --}}
</div>
@endsection
'@;

    "resources/views/books/partials/quick-view-modal.blade.php" = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 4
NHIỆM VỤ: Modal hiển thị nhanh thông tin sách mà không cần chuyển trang
=============================================================================
--}}
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><div class="modal-body">[TV4 Modal Quick View]</div></div></div>
</div>
'@;

    # TV5: FORM CHỈNH SỬA
    "resources/views/books/edit.blade.php"                      = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 5
NHIỆM VỤ: Form cập nhật thông tin sách (Mã sách chỉ đọc, thay ảnh cũ bằng ảnh mới)
=============================================================================
--}}
@extends('layouts.app')
@section('title', 'Cập nhật Đầu sách')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark"><h5>CẬP NHẬT ĐẦU SÁCH</h5></div>
        <div class="card-body">
            {{-- TV5 code form chỉnh sửa tại đây --}}
        </div>
    </div>
</div>
@endsection
'@;

    # TV6: THÙNG RÁC & KHÔI PHỤC
    "resources/views/books/trash.blade.php"                     = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 6
NHIỆM VỤ: Giao diện thùng rác các sách đã xóa mềm (Nút Khôi phục & Xóa vĩnh viễn)
=============================================================================
--}}
@extends('layouts.app')
@section('title', 'Thùng rác - Sách đã xóa')

@section('content')
<div class="container py-4">
    <h4 class="text-danger">THÙNG RÁC - ĐẦU SÁCH ĐÃ XÓA TẠM</h4>
    {{-- TV6 code bảng sách đã xóa tại đây --}}
</div>
@endsection
'@;

    # TV7: MODAL NHẬP THÊM HÀNG TỒN KHO
    "resources/views/books/partials/stock-modal.blade.php"      = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 7
NHIỆM VỤ: Modal Quản trị viên nhập thêm số lượng cho đầu sách đã có trong kho
=============================================================================
--}}
<div class="modal fade" id="stockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">NHẬP THÊM SỐ LƯỢNG KHO</h5></div>
            <div class="modal-body">
                {{-- TV7 code form nhập thêm số lượng tại đây --}}
            </div>
        </div>
    </div>
</div>
'@;

    # TV8: NÚT XUẤT EXCEL/PDF & PHÂN QUYỀN
    "resources/views/books/partials/action-buttons.blade.php"   = @'
{{-- 
=============================================================================
PHÂN CÔNG: THÀNH VIÊN 8
NHIỆM VỤ: Nút Xuất Excel/PDF và bọc các nút chức năng bằng @can('admin')
=============================================================================
--}}
<div class="d-flex gap-2 mb-3">
    <a href="#" class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Xuất Excel</a>
</div>
'@;
}

foreach ($item in $filesContent.GetEnumerator()) {
    $path = $item.Key
    $content = $item.Value
    Set-Content -Path $path -Value $content -Encoding UTF8
    Write-Host "   + Đã tạo: $path" -ForegroundColor Green
}

# 6. KHỞI TẠO SẴN TOÀN BỘ ROUTES VÀO routes/web.php
Write-Host "5. Đang cấu hình sẵn routes trong routes/web.php..." -ForegroundColor Yellow
$webRoutesContent = @'
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
'@

Set-Content -Path "routes/web.php" -Value $webRoutesContent -Encoding UTF8

Write-Host "`n>>> HOÀN THÀNH TẠO BỘ KHUNG! CẢ NHÓM ĐÃ SẴN SÀNG ĐỂ CODE!" -ForegroundColor Cyan
