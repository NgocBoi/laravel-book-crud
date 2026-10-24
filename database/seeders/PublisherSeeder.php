<?php

namespace Database\Seeders;

use App\Models\Publisher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PublisherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('data/books.json');
        if (!File::exists($jsonPath)) {
            $jsonPath = storage_path('app/public/books.json');
        }
        if (!File::exists($jsonPath)) {
            $jsonPath = public_path('books.json');
        }

        if (!File::exists($jsonPath)) {
            $this->command->error("Không tìm thấy file books.json tại bất kỳ đường dẫn nào.");
            return;
        }

        $booksData = json_decode(File::get($jsonPath), true);
        $publishers = [];

        foreach ($booksData as $b) {
            $pubName = trim($b['publisher'] ?? '');
            if ($pubName && !in_array($pubName, $publishers)) {
                $publishers[] = $pubName;
            }
        }

        foreach ($publishers as $name) {
            $existing = Publisher::where('name', $name)->first();
            if ($existing) {
                continue;
            }

            $baseSlug = Str::slug($name);
            $slug = $baseSlug ?: 'nxb-' . Str::random(5);
            $count = 1;
            while (Publisher::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            Publisher::create([
                'name' => $name,
                'slug' => $slug,
                'email' => 'contact@' . ($baseSlug ?: 'nxb') . '.vn',
                'phone' => '024 3825 ' . rand(1000, 9999),
                'address' => 'Hà Nội / TP. Hồ Chí Minh, Việt Nam',
                'description' => "Nhà xuất bản {$name} chuyên xuất bản và phát hành các ấn phẩm uy tín, chất lượng hàng đầu Việt Nam.",
            ]);
        }

        $this->command->info('Đã seed thành công ' . count($publishers) . ' nhà xuất bản vào database!');
    }
}
