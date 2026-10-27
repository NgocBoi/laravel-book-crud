{{-- Xuất Excel theo đúng bộ lọc đang chọn; chỉ admin được xuất dữ liệu. --}}
@if(auth()->user()?->isAdmin())
    <a href="{{ route('books.exportExcel', request()->only(\App\Models\Book::FILTER_KEYS)) }}" class="btn btn-outline-secondary">
        <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i> Xuất Excel
    </a>
@endif
