<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title', 'Book Management') | Book Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin-figma.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="admin-body @yield('body-class')">
    <nav class="navbar navbar-expand-lg admin-navbar sticky-top" aria-label="Điều hướng chính">
        <div class="container-fluid admin-shell px-3 px-lg-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('books.index') }}">
                <span class="brand-symbol"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                <span>Book Management</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Mở menu điều hướng">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-lg-auto">
                    <li class="nav-item">
                        <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}" @if(request()->routeIs('books.*')) aria-current="page" @endif>
                            <i class="bi bi-journal-bookmark me-1" aria-hidden="true"></i> Sách
                        </a>
                    </li>
                </ul>
                <span class="nav-context d-none d-lg-inline-flex"><i class="bi bi-grid-1x2 me-2" aria-hidden="true"></i> Danh mục sách</span>
               
                <!-- KHU VỰC ĐÃ CẬP NHẬT THEO GỢI Ý -->
                <div class="d-flex align-items-center gap-3 ms-lg-3">
                        @auth
                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-secondary fw-semibold">
                                {{ auth()->user()->name }} 
                                <span class="text-muted fw-normal">({{ auth()->user()->role }})</span>
                            </span>
                            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm">Đăng xuất</button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm">Đăng nhập</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="container-fluid admin-shell px-3 px-lg-4 py-4 py-lg-5">
        @if(session('success'))
            <div class="admin-alert alert-success mb-4" role="status">
                <i class="bi bi-check-circle-fill fs-5" aria-hidden="true"></i>
                <div class="flex-grow-1"><div class="alert-heading">Thao tác thành công</div><div class="alert-detail">{{ session('success') }}</div></div>
            </div>
        @endif
        @if(session('error'))
            <div class="admin-alert alert-danger mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5" aria-hidden="true"></i>
                <div class="flex-grow-1"><div class="alert-heading">Có lỗi xảy ra</div><div class="alert-detail">{{ session('error') }}</div></div>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
