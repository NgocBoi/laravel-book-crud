
@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<style>
    .login-page {
<<<<<<< HEAD
        min-height: 85vh;
=======
        min-height: 70vh;
>>>>>>> hoan-thien-web
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
<<<<<<< HEAD
        background: linear-gradient(135deg, #f0fdf4 0%, #f7fee7 50%, #ecfdf5 100%);
=======
        background: radial-gradient(circle at top, var(--ui-primary-light) 0%, transparent 65%);
>>>>>>> hoan-thien-web
    }

    .login-card {
        width: 100%;
        max-width: 450px;
        background: #ffffff;
<<<<<<< HEAD
        border: 1px solid #dcfce7;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 20px 55px rgba(22, 101, 52, 0.10);
=======
        border: 1px solid var(--ui-border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 20px 55px rgba(37, 99, 235, 0.10);
>>>>>>> hoan-thien-web
    }

    .login-header {
        text-align: center;
        padding: 35px 30px 25px;
<<<<<<< HEAD
        background: linear-gradient(135deg, #15803d, #16a34a);
=======
        background: linear-gradient(135deg, var(--ui-primary-hover), var(--ui-primary));
>>>>>>> hoan-thien-web
        color: #ffffff;
    }

    .login-icon {
        width: 68px;
        height: 68px;
        margin: 0 auto 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.17);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        font-size: 30px;
    }

    .login-header h4 {
        font-weight: 700;
        margin-bottom: 8px;
        letter-spacing: 0.4px;
    }

    .login-header p {
        margin: 0;
        font-size: 14px;
<<<<<<< HEAD
        color: #dcfce7;
=======
        color: #dbeafe;
>>>>>>> hoan-thien-web
    }

    .login-body {
        padding: 32px;
    }

    .login-label {
        display: block;
        margin-bottom: 9px;
<<<<<<< HEAD
        color: #166534;
=======
        color: #334155;
>>>>>>> hoan-thien-web
        font-size: 14px;
        font-weight: 600;
    }

    .login-input-wrap {
        position: relative;
    }

    .login-input-wrap > i {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
<<<<<<< HEAD
        color: #16a34a;
=======
        color: var(--ui-primary);
>>>>>>> hoan-thien-web
        font-size: 17px;
        pointer-events: none;
    }

    .login-input {
        min-height: 49px;
        padding: 12px 15px 12px 44px;
<<<<<<< HEAD
        border: 1px solid #d1fae5;
        border-radius: 12px;
        background: #f9fefb;
=======
        border: 1px solid #dce4ed;
        border-radius: 12px;
        background: #ffffff;
>>>>>>> hoan-thien-web
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .login-input:focus {
        background: #ffffff;
<<<<<<< HEAD
        border-color: #22c55e;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
=======
        border-color: #80a8ff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
>>>>>>> hoan-thien-web
    }

    .login-input.is-invalid {
        border-color: #dc3545;
    }

    .login-check {
<<<<<<< HEAD
        accent-color: #15803d;
=======
        accent-color: var(--ui-primary);
>>>>>>> hoan-thien-web
        cursor: pointer;
    }

    .login-check-label {
        color: #64748b;
        font-size: 13px;
        cursor: pointer;
    }

    .login-submit {
        min-height: 49px;
        border: none;
        border-radius: 12px;
<<<<<<< HEAD
        background: linear-gradient(135deg, #15803d, #22c55e);
=======
        background: var(--ui-primary);
>>>>>>> hoan-thien-web
        color: #ffffff;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.25s ease;
<<<<<<< HEAD
        box-shadow: 0 7px 18px rgba(21, 128, 61, 0.20);
    }

    .login-submit:hover {
        background: linear-gradient(135deg, #166534, #16a34a);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(21, 128, 61, 0.25);
=======
        box-shadow: 0 7px 18px rgba(37, 99, 235, 0.20);
    }

    .login-submit:hover {
        background: var(--ui-primary-hover);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.25);
>>>>>>> hoan-thien-web
    }

    .login-submit:active {
        transform: translateY(0);
    }

    .login-alert {
        border-radius: 10px;
        font-size: 14px;
    }

<<<<<<< HEAD
    .login-footer {
        padding-top: 22px;
        margin-top: 25px;
        border-top: 1px solid #ecfdf5;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
    }

    .login-footer i {
        color: #16a34a;
        margin-right: 5px;
    }

=======
>>>>>>> hoan-thien-web
    @media (max-width: 480px) {
        .login-body {
            padding: 25px 22px;
        }

        .login-header {
            padding: 28px 20px 22px;
        }
    }
</style>

<div class="login-page">
    <div class="login-card">

        <div class="login-header">
            <div class="login-icon">
                <i class="bi bi-book-half"></i>
            </div>
            <h4>ĐĂNG NHẬP HỆ THỐNG</h4>
            <p>Chào mừng bạn quay trở lại!</p>
        </div>

        <div class="login-body">

<<<<<<< HEAD
            @if (session('success'))
                <div class="alert alert-success login-alert" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

=======
>>>>>>> hoan-thien-web
            {{-- Hiển thị lỗi validation và lỗi đăng nhập --}}
            @if ($errors->any())
                <div class="alert alert-danger login-alert" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="email" class="login-label">
                        Địa chỉ Email
                    </label>

                    <div class="login-input-wrap">
                        <i class="bi bi-envelope"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control login-input @error('email') is-invalid @enderror"
                            placeholder="Nhập email của bạn"
                            required
                            autofocus
                            autocomplete="username"
                        >
                    </div>

                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="login-label">
                        Mật khẩu
                    </label>

                    <div class="login-input-wrap">
                        <i class="bi bi-lock"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control login-input @error('password') is-invalid @enderror"
                            placeholder="Nhập mật khẩu"
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input
                        class="form-check-input login-check"
                        type="checkbox"
                        name="remember"
                        id="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    <label class="form-check-label login-check-label" for="remember">
                        Ghi nhớ đăng nhập
                    </label>
                </div>

                <button type="submit" class="btn login-submit w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Đăng nhập
                </button>
            </form>

<<<<<<< HEAD
            <div class="login-footer">
                <i class="bi bi-shield-check"></i>
                Hệ thống quản lý sách
            </div>

=======
>>>>>>> hoan-thien-web
        </div>
    </div>
</div>
@endsection
