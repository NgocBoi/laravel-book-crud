
@extends('layouts.app')
@section('title', 'Đăng nhập')

@section('content')
<style>
    .login-page {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 16px;
        background: radial-gradient(circle at top, var(--ui-primary-light) 0%, transparent 65%);
    }

    .login-card {
        width: 100%;
        max-width: 450px;
        background: #ffffff;
        border: 1px solid var(--ui-border);
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 20px 55px rgba(37, 99, 235, 0.10);
    }

    .login-header {
        text-align: center;
        padding: 35px 30px 25px;
        background: linear-gradient(135deg, var(--ui-primary-hover), var(--ui-primary));
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
        color: #dbeafe;
    }

    .login-body {
        padding: 32px;
    }

    .login-label {
        display: block;
        margin-bottom: 9px;
        color: #334155;
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
        color: var(--ui-primary);
        font-size: 17px;
        pointer-events: none;
    }

    .login-input {
        min-height: 49px;
        padding: 12px 15px 12px 44px;
        border: 1px solid #dce4ed;
        border-radius: 12px;
        background: #ffffff;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .login-input:focus {
        background: #ffffff;
        border-color: #80a8ff;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .login-input.is-invalid {
        border-color: #dc3545;
    }

    .login-check {
        accent-color: var(--ui-primary);
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
        background: var(--ui-primary);
        color: #ffffff;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.25s ease;
        box-shadow: 0 7px 18px rgba(37, 99, 235, 0.20);
    }

    .login-submit:hover {
        background: var(--ui-primary-hover);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.25);
    }

    .login-submit:active {
        transform: translateY(0);
    }

    .login-alert {
        border-radius: 10px;
        font-size: 14px;
    }

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

        </div>
    </div>
</div>
@endsection
