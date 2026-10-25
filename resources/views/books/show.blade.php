@extends('layouts.app')
@section('title', 'Chi tiết Đầu sách')

@section('content')
<div class="container py-4">
    @include('books.partials.flash-messages')

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-3">
                    @include('books.partials.book-cover', ['book' => $book, 'class' => 'img-fluid rounded w-100'])
                </div>
                <div class="col-md-9">
                    <h3 class="fw-bold">{{ $book->title }}</h3>
                    <p class="text-danger fs-4 mb-2">{{ number_format($book->price, 0, ',', '.') }} đ</p>

                    <dl class="row mb-3">
                        <dt class="col-sm-3">Mã sách (ISBN)</dt>
                        <dd class="col-sm-9">{{ $book->isbn ?? '—' }}</dd>
                        <dt class="col-sm-3">Tác giả</dt>
                        <dd class="col-sm-9">{{ $book->authors->pluck('name')->join(', ') ?: '—' }}</dd>
                        <dt class="col-sm-3">Thể loại</dt>
                        <dd class="col-sm-9">
                            @forelse ($book->categories as $category)
                                <span class="badge text-bg-info">{{ $category->name }}</span>
                            @empty
                                —
                            @endforelse
                        </dd>
                        <dt class="col-sm-3">Nhà xuất bản</dt>
                        <dd class="col-sm-9">{{ $book->publisher?->name ?? '—' }}</dd>
                        <dt class="col-sm-3">Năm xuất bản</dt>
                        <dd class="col-sm-9">{{ $book->published_year ?? '—' }}</dd>
                        <dt class="col-sm-3">Số trang / Kích thước</dt>
                        <dd class="col-sm-9">{{ $book->page_count ?? '—' }} / {{ $book->dimensions ?? '—' }}</dd>
                        <dt class="col-sm-3">Tồn kho</dt>
                        <dd class="col-sm-9">
                            @include('books.partials.stock-badge', ['book' => $book])
                            @foreach ($book->warehouses as $warehouse)
                                <div class="small text-muted">{{ $warehouse->name }}: {{ $warehouse->pivot->quantity }}</div>
                            @endforeach
                        </dd>
                    </dl>

                    @if ($book->description)
                        <h6 class="fw-bold">Mô tả</h6>
                        <p class="text-muted">{!! nl2br(e($book->description)) !!}</p>
                    @endif

                    <div class="d-flex gap-2">
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                        <a href="{{ route('books.edit', $book) }}" class="btn btn-warning">Sửa</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
