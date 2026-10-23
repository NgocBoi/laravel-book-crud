<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Đường dẫn file JSON
        $jsonPath = storage_path('app/public/books.json');
        if (!File::exists($jsonPath)) {
            $jsonPath = public_path('books.json');
        }

        if (!File::exists($jsonPath)) {
            $this->command->error("Không tìm thấy file books.json tại: {$jsonPath}");
            return;
        }

        $booksData = json_decode(File::get($jsonPath), true);
        if (!$booksData || !is_array($booksData)) {
            $this->command->error("Dữ liệu trong books.json không hợp lệ.");
            return;
        }

        // Đảm bảo thư mục lưu trữ ảnh tồn tại
        $storageBooksDir = storage_path('app/public/books');
        if (!File::exists($storageBooksDir)) {
            File::makeDirectory($storageBooksDir, 0755, true);
        }

        // Lấy danh sách Categories để map nhanh
        $categories = Category::all()->pluck('id', 'name')->toArray();

        $seededCount = 0;
        foreach ($booksData as $item) {
            $categoryName = $item['category_name'] ?? 'Văn Học';
            $categoryId = $categories[$categoryName] ?? (Category::first()->id ?? 1);

            // Đồng bộ file ảnh từ public/books sang storage/app/public/books nếu chưa có
            $imageName = $item['cover_image'] ?? null;
            if ($imageName) {
                $targetPath = $storageBooksDir . DIRECTORY_SEPARATOR . $imageName;
                $publicImgPath = public_path('books' . DIRECTORY_SEPARATOR . $imageName);

                if (!File::exists($targetPath) && File::exists($publicImgPath)) {
                    File::copy($publicImgPath, $targetPath);
                }
            }

            Book::updateOrCreate(
                ['book_code' => $item['book_code']],
                [
                    'category_id' => $categoryId,
                    'isbn' => $item['isbn'] ?? null,
                    'title' => $item['title'],
                    'author' => $item['author'],
                    'translator' => $item['translator'] ?? null,
                    'publisher' => $item['publisher'] ?? null,
                    'supplier' => $item['supplier'] ?? null,
                    'publish_year' => $item['publish_year'] ?? null,
                    'language' => $item['language'] ?? 'Tiếng Việt',
                    'weight' => $item['weight'] ?? 250,
                    'dimensions' => $item['dimensions'] ?? null,
                    'pages' => $item['pages'] ?? 200,
                    'original_price' => $item['original_price'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'stock' => $item['stock'] ?? 0,
                    'sold_quantity' => $item['sold_quantity'] ?? 0,
                    'cover_image' => $imageName,
                    'description' => $item['description'] ?? null,
                    'is_trending' => (bool) ($item['is_trending'] ?? false),
                    'badge_type' => $item['badge_type'] ?? null,
                ]
            );

            $seededCount++;
        }

        $this->command->info("Đã seed thành công {$seededCount} đầu sách vào database!");
    }
}
