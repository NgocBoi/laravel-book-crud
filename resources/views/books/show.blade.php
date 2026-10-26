@extends('layouts.app')
@section('title', 'Chi tiết Đầu sách')

@section('content')
<style>
    /* Bảng màu theo thiết kế: xanh #2563eb, nền xám nhạt, thanh điều hướng trắng, badge dạng viên nhạt */
    :root { --bs-primary: #2563eb; --bs-primary-rgb: 37, 99, 235; --bs-link-color: #2563eb; --bs-border-color: #e5e7eb; }
    .btn-primary { --bs-btn-bg: #2563eb; --bs-btn-border-color: #2563eb; --bs-btn-hover-bg: #1d4ed8; --bs-btn-hover-border-color: #1d4ed8; --bs-btn-active-bg: #1d4ed8; --bs-btn-active-border-color: #1d4ed8; }
    .text-primary { color: #2563eb !important; }
    .navbar.bg-primary { background-color: #fff !important; border-bottom: 1px solid #e5e7eb; }
    .navbar.bg-primary .navbar-brand { color: #111827; }
    .navbar.bg-primary .nav-link { color: #4b5563; }
    .navbar.bg-primary .nav-link.active { color: #2563eb; font-weight: 600; }
    .navbar.bg-primary .navbar-toggler { border-color: #d1d5db; }
    .navbar.bg-primary .navbar-toggler-icon { filter: invert(1); }
    .badge { font-weight: 500; border-radius: 999px; padding: .3em .75em; white-space: nowrap; }
    .badge.text-bg-success { background-color: #dcfce7 !important; color: #15803d !important; }
    .badge.text-bg-warning { background-color: #fef3c7 !important; color: #b45309 !important; }
    .badge.text-bg-danger { background-color: #fee2e2 !important; color: #dc2626 !important; }
    body.bg-light { background-color: #f5f6f8 !important; }
    body { font-size: .9rem; }
    .card { border: 1px solid #e5e7eb !important; border-radius: .6rem; box-shadow: none !important; }
    .btn { font-size: .85rem; border-radius: .4rem; }
</style>
<div class="container py-4">
    <a href="{{ route('books.index') }}" class="text-decoration-none small"><i class="bi bi-arrow-left"></i> Quay lại danh sách</a>

    <div class="card border-0 shadow-sm mt-3">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    @if ($book->cover_url)
                        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="img-fluid rounded w-100">
                    @else
                        <div class="rounded bg-secondary-subtle d-flex align-items-center justify-content-center text-secondary" style="height:320px"><i class="bi bi-book" style="font-size:4rem"></i></div>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="d-flex justify-content-between align-items-start">
                        <h3 class="fw-bold">{{ $book->title }}</h3>
                        <div class="d-flex gap-2">
                            <a href="{{ route('books.edit', $book) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil-square"></i> Sửa</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        @foreach ($book->categories as $category)
                            <span class="badge text-bg-light border">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <div class="fs-3 fw-bold text-primary mb-3">{{ number_format($book->price, 0, ',', '.') }} ₫</div>

                    <dl class="row mb-3">
                        <dt class="col-sm-3">Tác giả</dt><dd class="col-sm-9">{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</dd>
                        <dt class="col-sm-3">Nhà xuất bản</dt><dd class="col-sm-9">{{ $book->publisher?->name ?? '—' }}</dd>
                        <dt class="col-sm-3">Tồn kho</dt><dd class="col-sm-9">@include('books.partials.stock-badge')</dd>
                        <dt class="col-sm-3">ISBN</dt><dd class="col-sm-9">{{ $book->isbn ?? '—' }}</dd>
                        <dt class="col-sm-3">Năm xuất bản</dt><dd class="col-sm-9">{{ $book->published_year ?? '—' }}</dd>
                        <dt class="col-sm-3">Số trang</dt><dd class="col-sm-9">{{ $book->page_count ?? '—' }}</dd>
                        <dt class="col-sm-3">Kích thước</dt><dd class="col-sm-9">{{ $book->dimensions ?? '—' }}</dd>
                    </dl>

                    @if ($book->description)
                        <h6 class="fw-bold">Mô tả</h6>
                        <p class="text-muted">{{ $book->description }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <h5 class="fw-bold mt-4 mb-3">Sách cùng thể loại</h5>
        <div class="row g-3">
            @foreach ($related as $item)
                <div class="col-6 col-md-3">
                    <a href="{{ route('books.show', $item) }}" class="card h-100 border-0 shadow-sm text-decoration-none text-body">
                        @if ($item->cover_url)
                            <img src="{{ $item->cover_url }}" alt="{{ $item->title }}" class="card-img-top" style="height:180px;object-fit:cover">
                        @else
                            <div class="bg-secondary-subtle d-flex align-items-center justify-content-center text-secondary" style="height:180px"><i class="bi bi-book fs-1"></i></div>
                        @endif
                        <div class="card-body p-2">
                            <div class="fw-semibold small">{{ $item->title }}</div>
                            <div class="text-muted small">{{ $item->authors->pluck('name')->join(', ') }}</div>
                            <div class="text-primary small">{{ number_format($item->price, 0, ',', '.') }} ₫</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
