{{-- Thông báo flash (success/error) dùng chung cho các trang sách --}}
@foreach (['success', 'error'] as $type)
    @if (session($type))
        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show" role="alert">
            {{ session($type) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif
@endforeach
