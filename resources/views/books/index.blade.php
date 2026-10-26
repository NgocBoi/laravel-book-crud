@extends('layouts.app')
@section('title', 'Danh mục Sách')

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
    .page-title { font-weight: 700; font-size: 1.75rem; color: #111827; }
    .card { border: 1px solid #e5e7eb !important; border-radius: .6rem; box-shadow: none !important; }
    .card-header { background: #fff; border-bottom: 1px solid #e5e7eb; }
    .table > thead th { font-size: .75rem; font-weight: 600; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; white-space: nowrap; }
    .table > :not(caption) > * > * { padding: .85rem 1rem; }
    .table-hover > tbody > tr:hover > * { background: #f9fafb; }
    .btn, .form-control, .form-select { font-size: .85rem; border-radius: .4rem; }
    .act { font-size: .8rem; text-decoration: none; margin-right: .75rem; background: none; border: 0; padding: 0; }
    .pagination { margin: 0; --bs-pagination-font-size: .8rem; }
</style>
<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-4">
        <div>
            <h1 class="page-title mb-1">Quản lý sách</h1>
            <p class="text-muted mb-0">Theo dõi thông tin, số lượng và danh mục sách</p>
        </div>
        <div class="d-flex gap-2">
            @include('books.partials.action-buttons')
            <a href="{{ route('books.create') }}" class="btn btn-primary d-flex align-items-center"><i class="bi bi-plus-lg me-1"></i> Thêm sách</a>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center py-3 px-4">
            <span class="fw-bold">Danh sách sách</span>
            <span class="text-muted small">{{ $books->total() }} bản ghi</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Ảnh bìa</th>
                        <th>Tên sách</th>
                        <th>Tác giả</th>
                        <th>Thể loại</th>
                        <th>Nhà xuất bản</th>
                        <th class="text-end">Giá</th>
                        <th>Tồn kho</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td>
                                @if ($book->cover_url)
                                    <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="rounded border" style="width:40px;height:56px;object-fit:cover">
                                @else
                                    <div class="rounded bg-light border d-flex align-items-center justify-content-center text-muted" style="width:40px;height:56px"><i class="bi bi-book"></i></div>
                                @endif
                            </td>
                            <td class="fw-semibold">{{ $book->title }}</td>
                            <td>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>{{ $book->publisher?->name ?? '—' }}</td>
                            <td class="text-end text-nowrap">{{ number_format($book->price, 0, ',', '.') }} ₫</td>
                            <td>@include('books.partials.stock-badge')</td>
                            <td class="text-nowrap">
                                <button type="button" class="act text-primary"
                                        data-bs-toggle="modal" data-bs-target="#quickViewModal"
                                        data-title="{{ $book->title }}"
                                        data-cover="{{ $book->cover_url }}"
                                        data-authors="{{ $book->authors->pluck('name')->join(', ') ?: '—' }}"
                                        data-categories="{{ $book->categories->pluck('name')->join(', ') ?: '—' }}"
                                        data-publisher="{{ $book->publisher?->name ?? '—' }}"
                                        data-price="{{ number_format($book->price, 0, ',', '.') }} ₫"
                                        data-stock="{{ $book->stock_quantity }}"
                                        data-url="{{ route('books.show', $book) }}">
                                    <i class="bi bi-eye"></i> Xem
                                </button>
                                <a href="{{ route('books.edit', $book) }}" class="act text-primary"><i class="bi bi-pencil-square"></i> Sửa</a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xóa sách này?')">
                                    @csrf @method('DELETE')
                                    <button class="act text-danger me-0"><i class="bi bi-trash"></i> Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-5">Không tìm thấy sách nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex justify-content-between align-items-center px-4 py-3">
            <span class="text-muted small">Hiển thị {{ $books->count() }} kết quả</span>
            {{ $books->links() }}
        </div>
    </div>
</div>

@include('books.partials.quick-view-modal')
@endsection
