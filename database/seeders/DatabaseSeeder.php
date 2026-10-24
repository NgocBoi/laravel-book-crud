<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Quản trị viên', 'password' => Hash::make('12345678'), 'role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Nhân viên', 'password' => Hash::make('12345678'), 'role' => 'user']
        );

        // Thứ tự quan trọng: bảng cha trước, bảng con sau
        $this->call([
            TheLoaiSeeder::class,
            TacGiaSeeder::class,
            NhaXuatBanSeeder::class,
            SachSeeder::class,
        ]);

        // Bảng kho và ton_kho để trống, nhập dữ liệu sau
    }
}
