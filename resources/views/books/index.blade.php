@extends('layouts.app')
@section('title', 'Quản lý sách')

@section('content')
@php($isAdmin = auth()->user()?->isAdmin())
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <div class="eyebrow mb-2">Danh mục / Sách</div>
        <h1 class="page-title">Quản lý sách</h1>
    </div>
    <div class="page-actions">
        @include('books.partials.action-buttons')
        @if($isAdmin)
            <a href="{{ route('books.trash') }}" class="btn btn-outline-secondary"><i class="bi bi-trash3 me-1" aria-hidden="true"></i> Thùng rác</a>
            <a href="{{ route('books.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Thêm sách</a>
        @endif
    </div>
</div>

@include('books.partials.filter-bar')

<section class="section-card" aria-labelledby="books-heading">
    <header class="section-card-header">
        <h2 class="section-title" id="books-heading">Danh sách sách</h2>
        <span class="section-note">{{ $books->total() }} bản ghi</span>
    </header>
    <div class="table-responsive">
        <table class="table table-hover admin-table align-middle">
            <thead>
                <tr>
                    <th scope="col">Ảnh bìa</th>
                    <th scope="col">Tên sách / ISBN</th>
                    <th scope="col">Tác giả</th>
                    <th scope="col">Thể loại</th>
                    <th scope="col">Nhà xuất bản</th>
                    <th scope="col" class="text-end">Giá bán</th>
                    <th scope="col">Tồn kho</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    <tr>
                        <td>
                            @if($book->cover_url)
                                <img src="{{ $book->cover_url }}" alt="Bìa sách {{ $book->title }}" class="book-cover" loading="lazy">
                            @else
                                <span class="cover-placeholder" role="img" aria-label="Sách chưa có ảnh bìa"><i class="bi bi-book" aria-hidden="true"></i></span>
                            @endif
                        </td>
                        <td>
                            <a class="book-name" href="{{ route('books.show', $book) }}">{{ $book->title }}</a>
                            <div class="meta-text mt-1">ISBN: {{ $book->isbn ?: 'Chưa cập nhật' }}</div>
                        </td>
                        <td>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>{{ $book->publisher?->name ?? '—' }}</td>
                        <td class="text-end text-nowrap fw-semibold">{{ number_format($book->price, 0, ',', '.') }} ₫</td>
                        <td>@include('books.partials.stock-badge')</td>
                        <td>
                            <div class="action-group">
                                <button type="button" class="action-link" data-bs-toggle="modal" data-bs-target="#quickViewModal"
                                        data-title="{{ $book->title }}" data-cover="{{ $book->cover_url }}"
                                        data-authors="{{ $book->authors->pluck('name')->join(', ') ?: '—' }}"
                                        data-categories="{{ $book->categories->pluck('name')->join(', ') ?: '—' }}"
                                        data-publisher="{{ $book->publisher?->name ?? '—' }}"
                                        data-price="{{ number_format($book->price, 0, ',', '.') }} ₫"
                                        data-stock="{{ $book->stock_quantity }}" data-url="{{ route('books.show', $book) }}"
                                        aria-label="Xem nhanh sách {{ $book->title }}">
                                    <i class="bi bi-eye me-1" aria-hidden="true"></i>Xem
                                </button>
                                @if($isAdmin)
                                    <a class="action-link" href="{{ route('books.edit', $book) }}" aria-label="Sửa sách {{ $book->title }}"><i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Sửa</a>
                                    <button type="button" class="action-link danger" data-bs-toggle="modal" data-bs-target="#deleteBook{{ $book->id }}" aria-label="Xóa sách {{ $book->title }}">
                                        <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Xóa
                                    </button>
                                @endif
                            </div>
                            @if($isAdmin)
                                <div class="modal fade" id="deleteBook{{ $book->id }}" tabindex="-1" aria-labelledby="deleteBookTitle{{ $book->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                                        <div class="modal-header px-4 py-3">
                                            <h2 class="modal-title fs-5 fw-bold" id="deleteBookTitle{{ $book->id }}"><i class="bi bi-exclamation-triangle text-danger me-2" aria-hidden="true"></i>Xác nhận xóa sách</h2>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body px-4 py-3">
                                            Bạn có chắc muốn xóa sách <strong>{{ $book->title }}</strong>?
                                            <p class="text-secondary small mt-2 mb-0">Thao tác sẽ gửi yêu cầu xóa đến hệ thống.</p>
                                        </div>
                                        <div class="modal-footer px-4 py-3">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <form action="{{ route('books.destroy', $book) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1" aria-hidden="true"></i> Xác nhận xóa</button>
                                            </form>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-journal-x" aria-hidden="true"></i></div>
                            <h3>{{ request()->hasAny(\App\Models\Book::FILTER_KEYS) ? 'Không tìm thấy sách phù hợp' : 'Chưa có sách nào' }}</h3>
                            <p>{{ request()->hasAny(\App\Models\Book::FILTER_KEYS) ?'Hãy thử thay đổi từ khóa hoặc các điều kiện lọc để xem kết quả khác.' : 'Danh mục hiện chưa có dữ liệu. Bạn có thể bắt đầu bằng cách thêm sách mới.' }}</p>
                            @if(request()->hasAny(\App\Models\Book::FILTER_KEYS))
                                <a class="btn btn-outline-primary" href="{{ route('books.index') }}"><i class="bi bi-arrow-counterclockwise me-1"></i> Xóa bộ lọc</a>
                            @elseif($isAdmin)
                                <a class="btn btn-primary" href="{{ route('books.create') }}"><i class="bi bi-plus-lg me-1"></i> Thêm sách</a>
                            @endif
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($books->hasPages() || $books->count())
        <footer class="card-footer-admin d-flex justify-content-between align-items-center">
            <span class="section-note">Hiển thị {{ $books->firstItem() ?? 0 }}–{{ $books->lastItem() ?? 0 }} trong {{ $books->total() }} bản ghi</span>
            {{ $books->onEachSide(1)->links('pagination::bootstrap-5') }}
        </footer>
    @endif
</section>

@include('books.partials.quick-view-modal')
@endsection
