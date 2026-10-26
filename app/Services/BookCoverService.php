<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Lưu trữ ảnh bìa sách trên disk "public" (storage/app/public/covers).
 * Cột books.cover_image_url lưu đường dẫn tương đối (covers/xxx.jpg) hoặc một link ngoài (http...).
 * Cần chạy `php artisan storage:link` một lần để ảnh truy cập được qua /storage/...
 */
class BookCoverService
{
    public const DISK = 'public';

    public const DIRECTORY = 'covers';

    /**
     * Lưu file ảnh vừa upload, trả về đường dẫn để ghi vào cover_image_url.
     * Không có file thì trả về null.
     */
    public function store(?UploadedFile $file): ?string
    {
        if (! $file) {
            return null;
        }

        // Tên file ngẫu nhiên (hashName) để không trùng và không lộ tên gốc
        return $file->store(self::DIRECTORY, self::DISK) ?: null;
    }

    /**
     * Xóa file ảnh đã lưu. Bỏ qua khi rỗng hoặc là link ngoài (không nằm trên server).
     */
    public function delete(?string $path): void
    {
        if ($path && ! $this->isExternal($path)) {
            Storage::disk(self::DISK)->delete($path);
        }
    }

    /**
     * Link hiển thị ảnh trong giao diện: link ngoài giữ nguyên, file đã lưu lấy URL từ storage.
     */
    public function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return $this->isExternal($path) ? $path : Storage::disk(self::DISK)->url($path);
    }

    private function isExternal(string $path): bool
    {
        return Str::startsWith($path, ['http://', 'https://']);
    }
}
