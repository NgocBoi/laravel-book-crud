<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaXuatBanSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('nha_xuat_ban')->insert([
            ['ma_nha_xuat_ban' => 1, 'ten_nha_xuat_ban' => 'Thế Giới', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 2, 'ten_nha_xuat_ban' => 'Phụ Nữ Việt Nam', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 3, 'ten_nha_xuat_ban' => 'Dân Trí', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 4, 'ten_nha_xuat_ban' => 'Lao Động', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 5, 'ten_nha_xuat_ban' => 'Công Thương', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 6, 'ten_nha_xuat_ban' => 'Văn học', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 7, 'ten_nha_xuat_ban' => 'Hà Nội', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 8, 'ten_nha_xuat_ban' => 'NXB Giáo Dục', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 9, 'ten_nha_xuat_ban' => 'NXB Đại học Quốc gia Hà Nội', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 10, 'ten_nha_xuat_ban' => 'Hồng Đức', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 11, 'ten_nha_xuat_ban' => 'Thanh Niên', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 12, 'ten_nha_xuat_ban' => 'NXB Kim Đồng', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 13, 'ten_nha_xuat_ban' => 'NXB Tổng Hợp TPHCM', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 14, 'ten_nha_xuat_ban' => 'Đại học Quốc Gia Hà Nội', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 15, 'ten_nha_xuat_ban' => 'NXB Thông Tấn', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 16, 'ten_nha_xuat_ban' => 'NXB Hội Nhà Văn', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 17, 'ten_nha_xuat_ban' => 'NXB Trẻ', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 18, 'ten_nha_xuat_ban' => 'NXB Văn Hoá Thông Tin', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 19, 'ten_nha_xuat_ban' => 'Quân Đội Nhân Dân', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
            ['ma_nha_xuat_ban' => 20, 'ten_nha_xuat_ban' => 'Kim Đồng', 'dia_chi' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam'],
        ]);
    }
}
