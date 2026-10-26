<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    
    //  Chỉ cho phép tài khoản role = 'admin' đi tiếp.
    //   Phải đặt SAU middleware 'auth' để $request->user() không bị null.
    
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->isAdmin()) {
            abort(403, 'Chỉ quản trị viên mới được thực hiện thao tác này.');
        }

        return $next($request);
    }
}
