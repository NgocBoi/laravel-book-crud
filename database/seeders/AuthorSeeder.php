<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = storage_path('app/public/books.json');
        if (!File::exists($jsonPath)) {
            $jsonPath = public_path('books.json');
        }

        if (!File::exists($jsonPath)) {
            $this->command->error("Không tìm thấy books.json");
            return;
        }

        $booksData = json_decode(File::get($jsonPath), true);
        $authors = [];

        foreach ($booksData as $b) {
            $authorName = trim($b['author'] ?? '');
            if ($authorName && !in_array($authorName, $authors)) {
                $authors[] = $authorName;
            }
        }

        foreach ($authors as $name) {
            $existing = Author::where('name', $name)->first();
            if ($existing) {
                continue;
            }

            $baseSlug = Str::slug($name);
            $slug = $baseSlug ?: 'tac-gia-' . Str::random(5);
            $count = 1;
            while (Author::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $count++;
            }

            Author::create([
                'name' => $name,
                'slug' => $slug,
                'bio' => "Tác giả {$name} với các tác phẩm tiêu biểu được đông đảo bạn đọc yêu mến và đón nhận.",
                'avatar' => null,
            ]);
        }

        $this->command->info('Đã seed thành công ' . count($authors) . ' tác giả vào database!');
    }
}
