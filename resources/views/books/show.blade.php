@extends('layouts.app')
@section('title', 'Chi tiết sách')

@section('content')
<nav aria-label="Đường dẫn" class="mb-3">
    <ol class="admin-breadcrumb"><li><a href="{{ route('books.index') }}">Sách</a></li><li>/</li><li class="current" aria-current="page">Chi tiết sách</li></ol>
</nav>
<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div><h1 class="page-title">Chi tiết sách</h1><p class="page-subtitle">Thông tin xuất bản, giá bán và số lượng tồn kho.</p></div>
    <div class="page-actions">
        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Danh sách sách</a>
        <a href="{{ route('books.edit', $book) }}" class="btn btn-primary"><i class="bi bi-pencil-square me-1"></i> Chỉnh sửa</a>
    </div>
</div>
<section class="section-card mb-4" aria-labelledby="book-detail-heading">
    <div class="section-card-body">
        <div class="row g-4 g-xl-5">
            <div class="col-md-4 col-xl-3">
                @if($book->cover_url)
                    <img src="{{ $book->cover_url }}" alt="Ảnh bìa {{ $book->title }}" class="w-100 rounded-3 border" style="max-height:360px;object-fit:contain;background:#f8fbff">
                @else
                    <div class="book-image-empty" style="min-height:300px"><i class="bi bi-book" aria-hidden="true"></i><span>Chưa có ảnh bìa</span></div>
                @endif
            </div>
            <div class="col-md-8 col-xl-9">
                <div class="d-flex align-items-start flex-wrap justify-content-between gap-2 mb-3">
                    <div>
                        <div class="eyebrow mb-2">Thông tin đầu sách</div>
                        <h2 class="h3 fw-bold mb-2" id="book-detail-heading">{{ $book->title }}</h2>
                        <p class="text-secondary small mb-0">Mã ISBN: {{ $book->isbn ?: 'Chưa cập nhật' }}</p>
                    </div>
                    @include('books.partials.stock-badge')
                </div>
                <div class="d-flex gap-2 flex-wrap mb-3">
                    @forelse($book->categories as $category)
                        <span class="inline-state" style="background:#eff6ff;color:#2563eb">{{ $category->name }}</span>
                    @empty
                        <span class="section-note">Chưa phân loại</span>
                    @endforelse
                </div>
                <div class="fs-3 fw-bold mb-4" style="color:#2563eb">{{ number_format($book->price, 0, ',', '.') }} ₫</div>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6"><div class="summary-box h-100"><dl class="mb-0"><dt>Tác giả</dt><dd>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</dd></dl></div></div>
                    <div class="col-sm-6"><div class="summary-box h-100"><dl class="mb-0"><dt>Nhà xuất bản</dt><dd>{{ $book->publisher?->name ?? '—' }}</dd></dl></div></div>
                    <div class="col-sm-6 col-lg-3"><div class="summary-box h-100"><dl class="mb-0"><dt>Năm xuất bản</dt><dd>{{ $book->published_year ?: '—' }}</dd></dl></div></div>
                    <div class="col-sm-6 col-lg-3"><div class="summary-box h-100"><dl class="mb-0"><dt>Số trang</dt><dd>{{ $book->page_count ?: '—' }}</dd></dl></div></div>
                    <div class="col-sm-6 col-lg-3"><div class="summary-box h-100"><dl class="mb-0"><dt>Kích thước</dt><dd>{{ $book->dimensions ?: '—' }}</dd></dl></div></div>
                    <div class="col-sm-6 col-lg-3"><div class="summary-box h-100"><dl class="mb-0"><dt>Số lượng tồn kho</dt><dd>{{ $book->stock_quantity }} quyển</dd></dl></div></div>
                </div>
                <h3 class="section-title mb-2">Mô tả sách</h3>
                <p class="text-secondary mb-0" style="line-height:1.85;white-space:pre-line">{{ $book->description ?: 'Chưa có mô tả cho đầu sách này.' }}</p>
            </div>
        </div>
    </div>
</section>
@if($related->isNotEmpty())
    <section aria-labelledby="related-heading">
        <h2 class="section-title mb-3" id="related-heading">Sách cùng thể loại</h2>
        <div class="row g-3">
            @foreach($related as $item)
                <div class="col-12 col-sm-6 col-lg-3">
                    <a href="{{ route('books.show', $item) }}" class="section-card d-block h-100 text-decoration-none text-body">
                        @if($item->cover_url)
                            <img src="{{ $item->cover_url }}" alt="Bìa sách {{ $item->title }}" style="width:100%;height:175px;object-fit:contain;background:#f8fbff" loading="lazy">
                        @else
                            <div class="book-image-empty" style="height:175px;min-height:175px;border:0;border-radius:0"><i class="bi bi-book"></i></div>
                        @endif
                        <div class="p-3">
                            <div class="fw-semibold mb-1">{{ $item->title }}</div>
                            <div class="section-note mb-2">{{ $item->authors->pluck('name')->join(', ') ?: 'Chưa cập nhật tác giả' }}</div>
                            <div class="fw-bold" style="color:#2563eb">{{ number_format($item->price, 0, ',', '.') }} ₫</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endif
@endsection
