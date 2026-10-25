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

{{-- Nhúng các Modal --}}
@include('books.partials.quick-view-modal')
@endsection
