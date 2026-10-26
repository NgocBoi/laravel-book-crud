@extends('layouts.app')
@section('title', 'Chỉnh sửa sách')
@section('body-class', 'book-form-page')

@section('content')
<header class="book-form-heading">
    <div>
        <h1 class="page-title">Chỉnh sửa sách</h1>
        <nav aria-label="Đường dẫn">
            <ol class="admin-breadcrumb">
                <li><a href="{{ route('books.index') }}">Sách</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Chỉnh sửa</li>
            </ol>
        </nav>
    </div>
    <a href="{{ route('books.index') }}" class="btn btn-outline-secondary"><span aria-hidden="true">←</span> Quay lại</a>
</header>
@include('books.partials.form', ['mode' => 'edit'])
@endsection
