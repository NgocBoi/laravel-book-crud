@extends('layouts.app')
@section('title', 'Cập nhật Đầu sách')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0">CẬP NHẬT ĐẦU SÁCH</h5></div>
        <div class="card-body">
            @include('books.partials.flash-messages')

            <form action="{{ route('books.update', $book) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('books.partials.book-form', ['book' => $book])

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Cập nhật</button>
                    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
