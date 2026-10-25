@extends('layouts.app')
@section('title', 'Danh mục Sách')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold text-primary mb-0">QUẢN LÝ ĐẦU SÁCH</h3>
        <a href="{{ route('books.create') }}" class="btn btn-success"><i class="bi bi-plus-lg"></i> Thêm sách</a>
    </div>

    @include('books.partials.flash-messages')
    @include('books.partials.action-buttons')

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 90px;">Ảnh bìa</th>
                        <th>Tên sách</th>
                        <th>Tác giả</th>
                        <th>Thể loại</th>
                        <th>NXB</th>
                        <th class="text-end text-nowrap">Giá</th>
                        <th class="text-center">Tồn kho</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($books as $book)
                        <tr>
                            <td style="width: 90px;">@include('books.partials.book-cover', ['book' => $book, 'class' => 'img-thumbnail'])</td>
                            <td>
                                <a href="{{ route('books.show', $book) }}" class="fw-semibold text-decoration-none">{{ $book->title }}</a>
                                <div class="small text-muted">{{ $book->isbn }}</div>
                            </td>
                            <td>{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</td>
                            <td>
                                @foreach ($book->categories as $category)
                                    <span class="badge text-bg-info">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $book->publisher?->name ?? '—' }}</td>
                            <td class="text-end text-nowrap">{{ number_format($book->price, 0, ',', '.') }} đ</td>
                            <td class="text-center">@include('books.partials.stock-badge', ['book' => $book])</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary" title="Chi tiết"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('books.edit', $book) }}" class="btn btn-sm btn-outline-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('books.destroy', $book) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm(@js('Xóa sách "' . $book->title . '"?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">Chưa có đầu sách nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $books->withQueryString()->links('pagination::bootstrap-5') }}</div>
</div>

@include('books.partials.quick-view-modal')
@include('books.partials.stock-modal')
@endsection
