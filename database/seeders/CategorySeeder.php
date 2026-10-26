<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['id' => 1, 'name' => 'Tâm Lý - Kĩ Năng Sống'],
            ['id' => 2, 'name' => 'Văn Học'],
            ['id' => 3, 'name' => 'Kinh Tế'],
            ['id' => 4, 'name' => 'Thiếu Nhi'],
            ['id' => 5, 'name' => 'Sách Học Ngoại Ngữ'],
            ['id' => 6, 'name' => 'Sách Giáo Khoa - Tham Khảo'],
        ]);
    }
}
