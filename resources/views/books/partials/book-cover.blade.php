{{-- Ảnh bìa: nhận $book, $class (tùy chọn). Hỗ trợ URL tuyệt đối hoặc đường dẫn trong storage/public --}}
@php
    $cover = $book->cover_image_url;
    $src = $cover
        ? (\Illuminate\Support\Str::startsWith($cover, ['http://', 'https://']) ? $cover : asset('storage/' . ltrim($cover, '/')))
        : null;
@endphp
@if ($src)
    <img src="{{ $src }}" alt="{{ $book->title }}" class="{{ $class ?? 'img-thumbnail' }}" style="object-fit: cover;">
@else
    <div class="bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center {{ $class ?? 'img-thumbnail' }}" style="min-height: 80px;">
        <i class="bi bi-book fs-3"></i>
    </div>
@endif
