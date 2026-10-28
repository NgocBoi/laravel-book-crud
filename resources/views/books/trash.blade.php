@extends('layouts.app')
@section('title', 'Thùng rác - Sách đã xóa')

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
    <div>
        <div class="eyebrow mb-2">Danh mục / Sách / Thùng rác</div>
        <h1 class="page-title">Thùng rác</h1>
        <p class="page-subtitle">Lưu vết các đầu sách đã bị xóa. Có thể khôi phục hoặc xóa vĩnh viễn.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1" aria-hidden="true"></i> Về danh sách sách</a>
    </div>
</div>

<section class="section-card" aria-labelledby="trash-heading">
    <header class="section-card-header">
        <h2 class="section-title" id="trash-heading">Sách đã xóa</h2>
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
                    <th scope="col">Thời điểm xóa</th>
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
                            <span class="book-name">{{ $book->title }}</span>
                            <div class="meta-text mt-1">ISBN: {{ $book->isbn ?: 'Chưa cập nhật' }}</div>
                        </td>
                        <td>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>{{ $book->categories->pluck('name')->join(', ') ?: '—' }}</td>
                        <td>{{ $book->publisher?->name ?? '—' }}</td>
                        <td class="text-nowrap">{{ $book->deleted_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="action-group">
                                <form action="{{ route('books.restore', $book->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="action-link" aria-label="Khôi phục sách {{ $book->title }}"><i class="bi bi-arrow-counterclockwise me-1" aria-hidden="true"></i>Khôi phục</button>
                                </form>
                                <button type="button" class="action-link danger" data-bs-toggle="modal" data-bs-target="#forceDeleteBook{{ $book->id }}" aria-label="Xóa vĩnh viễn sách {{ $book->title }}">
                                    <i class="bi bi-trash3 me-1" aria-hidden="true"></i>Xóa vĩnh viễn
                                </button>
                            </div>
                            <div class="modal fade" id="forceDeleteBook{{ $book->id }}" tabindex="-1" aria-labelledby="forceDeleteBookTitle{{ $book->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
                                    <div class="modal-header px-4 py-3">
                                        <h2 class="modal-title fs-5 fw-bold" id="forceDeleteBookTitle{{ $book->id }}"><i class="bi bi-exclamation-triangle text-danger me-2" aria-hidden="true"></i>Xóa vĩnh viễn sách</h2>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                    </div>
                                    <div class="modal-body px-4 py-3">
                                        Bạn có chắc muốn xóa vĩnh viễn sách <strong>{{ $book->title }}</strong>?
                                        <p class="text-secondary small mt-2 mb-0">Dữ liệu và ảnh bìa sẽ bị xóa hẳn, không thể khôi phục.</p>
                                    </div>
                                    <div class="modal-footer px-4 py-3">
                                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <form action="{{ route('books.forceDelete', $book->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="bi bi-trash3 me-1" aria-hidden="true"></i> Xóa vĩnh viễn</button>
                                        </form>
                                    </div>
                                </div></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="bi bi-trash3" aria-hidden="true"></i></div>
                            <h3>Thùng rác trống</h3>
                            <p>Chưa có sách nào bị xóa.</p>
                        </div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($books->hasPages())
        <footer class="card-footer-admin d-flex justify-content-between align-items-center">
            <span class="section-note">Hiển thị {{ $books->firstItem() }}–{{ $books->lastItem() }} trong {{ $books->total() }} bản ghi</span>
            {{ $books->onEachSide(1)->links('pagination::bootstrap-5') }}
        </footer>
    @endif
</section>
@endsection
