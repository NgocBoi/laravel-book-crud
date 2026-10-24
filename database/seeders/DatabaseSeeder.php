<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            AuthorSeeder::class,
            PublisherSeeder::class,
            BookSeeder::class,
        ]);

        // Tài khoản Quản trị viên (Admin)
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Quản trị viên',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        // Tài khoản Người dùng thường (Staff / User)
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Nhân viên',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );
    }
}
