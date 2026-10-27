<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    //  Hiển thị form đăng nhập.

    public function showLoginForm(): View|RedirectResponse
    {
        // Đã đăng nhập rồi thì không cho vào lại trang login
        if (Auth::check()) {
            return redirect()->route('books.index');
        }

        return view('auth.login');
    }

    //   Xử lý đăng nhập.

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ], [
            'email' => 'email',
            'password' => 'mật khẩu',
        ]);

        // remember = checkbox "Ghi nhớ đăng nhập"
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.']);
        }

        // Chống session fixation: đổi session id sau khi đăng nhập thành công
        $request->session()->regenerate();

        // Quay lại trang người dùng định vào, mặc định là danh sách sách
        return redirect()->intended(route('books.index'));
    }

    //  * Đăng xuất.

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Bạn đã đăng xuất.');
    }
}
