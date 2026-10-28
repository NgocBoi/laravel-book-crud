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
    <nav class="navbar admin-navbar sticky-top" aria-label="Điều hướng chính">
        <div class="container-fluid admin-shell px-3 px-lg-4">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="{{ route('books.index') }}">
                <span class="brand-symbol"><i class="bi bi-book-half" aria-hidden="true"></i></span>
                <span>Book Management</span>
            </a>
            @auth
                @php($currentUser = auth()->user())
                <div class="dropdown ms-auto">
                    <button class="user-menu dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($currentUser->name, 0, 1)) }}</span>
                        <span class="text-start lh-sm">
                            <span class="d-block fw-semibold">{{ $currentUser->name }}</span>
                            <span class="user-role">{{ $currentUser->isAdmin() ? 'Quản trị viên' : 'Người dùng' }} ({{ $currentUser->role }})</span>
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                        <li class="dropdown-header">{{ $currentUser->email }}</li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2" aria-hidden="true"></i>Đăng xuất</button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    @if(session('logout_success'))
        <div class="alert alert-success alert-dismissible fade show logout-toast" role="status">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>{{ session('logout_success') }}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif

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

    <footer class="admin-footer">
        <div class="container-fluid admin-shell px-3 px-lg-4">
            <span>© {{ now()->year }} Book Management</span>
            <span>Hệ thống quản lý sách</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
