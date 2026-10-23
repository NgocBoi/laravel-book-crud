<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tâm Lý - Kĩ Năng Sống',
                'description' => 'Sách phát triển bản thân, tư duy tích cực, tâm lý học ứng dụng và kỹ năng sống thành công.',
            ],
            [
                'name' => 'Văn Học',
                'description' => 'Tiểu thuyết, truyện ngắn, thơ ca, tản văn và các tác phẩm văn học kinh điển trong nước và quốc tế.',
            ],
            [
                'name' => 'Kinh Tế',
                'description' => 'Sách quản trị kinh doanh, khởi nghiệp, tài chính đầu tư, marketing và kinh tế vĩ mô.',
            ],
            [
                'name' => 'Thiếu Nhi',
                'description' => 'Truyện tranh, sách khám phá khoa học, truyện cổ tích và giáo dục kỹ năng cho lứa tuổi thiếu nhi.',
            ],
            [
                'name' => 'Sách Học Ngoại Ngữ',
                'description' => 'Giáo trình luyện thi, ngữ pháp, từ vựng tiếng Anh, tiếng Hàn, tiếng Nhật, tiếng Trung.',
            ],
            [
                'name' => 'Sách Giáo Khoa - Tham Khảo',
                'description' => 'Sách giáo khoa, sách bài tập, tài liệu ôn thi và nâng cao kiến thức các cấp học phổ thông.',
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['name' => $cat['name']],
                [
                    'slug' => Str::slug($cat['name']),
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
