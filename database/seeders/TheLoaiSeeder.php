<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TheLoaiSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('the_loai')->insert([
            ['ma_the_loai' => 1, 'ten_the_loai' => 'Tâm Lý - Kĩ Năng Sống', 'mo_ta' => 'Sách phát triển bản thân, tư duy tích cực, tâm lý học ứng dụng và kỹ năng sống thành công.'],
            ['ma_the_loai' => 2, 'ten_the_loai' => 'Văn Học', 'mo_ta' => 'Tiểu thuyết, truyện ngắn, thơ ca, tản văn và các tác phẩm văn học kinh điển trong nước và quốc tế.'],
            ['ma_the_loai' => 3, 'ten_the_loai' => 'Kinh Tế', 'mo_ta' => 'Sách quản trị kinh doanh, khởi nghiệp, tài chính đầu tư, marketing và kinh tế vĩ mô.'],
            ['ma_the_loai' => 4, 'ten_the_loai' => 'Thiếu Nhi', 'mo_ta' => 'Truyện tranh, sách khám phá khoa học, truyện cổ tích và giáo dục kỹ năng cho lứa tuổi thiếu nhi.'],
            ['ma_the_loai' => 5, 'ten_the_loai' => 'Sách Học Ngoại Ngữ', 'mo_ta' => 'Giáo trình luyện thi, ngữ pháp, từ vựng tiếng Anh, tiếng Hàn, tiếng Nhật, tiếng Trung.'],
            ['ma_the_loai' => 6, 'ten_the_loai' => 'Sách Giáo Khoa - Tham Khảo', 'mo_ta' => 'Sách giáo khoa, sách bài tập, tài liệu ôn thi và nâng cao kiến thức các cấp học phổ thông.'],
        ]);
    }
}
