<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Đăng ký các Gate phân quyền dùng chung.
     */
    public function boot(): void
    {
        // Dùng trong Blade: @can('admin') ... @endcan
        Gate::define('admin', fn (User $user): bool => $user->isAdmin());
    }
}
