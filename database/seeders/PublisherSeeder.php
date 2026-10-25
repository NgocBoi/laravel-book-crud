<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublisherSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('publishers')->insert([
            ['id' => 1, 'name' => 'Thế Giới', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 2, 'name' => 'Phụ Nữ Việt Nam', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 3, 'name' => 'Dân Trí', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 4, 'name' => 'Lao Động', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 5, 'name' => 'Công Thương', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 6, 'name' => 'Văn học', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 7, 'name' => 'Hà Nội', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 8, 'name' => 'NXB Giáo Dục', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 9, 'name' => 'NXB Đại học Quốc gia Hà Nội', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 10, 'name' => 'Hồng Đức', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 11, 'name' => 'Thanh Niên', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 12, 'name' => 'NXB Kim Đồng', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 13, 'name' => 'NXB Tổng Hợp TPHCM', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 14, 'name' => 'Đại học Quốc Gia Hà Nội', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 15, 'name' => 'NXB Thông Tấn', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 16, 'name' => 'NXB Hội Nhà Văn', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 17, 'name' => 'NXB Trẻ', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 18, 'name' => 'NXB Văn Hoá Thông Tin', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 19, 'name' => 'Quân Đội Nhân Dân', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['id' => 20, 'name' => 'Kim Đồng', 'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
        ]);
    }
}
