<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Book extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    /** Ngưỡng cảnh báo sắp hết hàng. */
    public const LOW_STOCK = 5;

    /** Thư mục lưu ảnh bìa trên disk "public". */
    public const COVER_DIR = 'covers';

    protected $fillable = [
        'isbn',
        'title',
        'published_year',
        'price',
        'stock_quantity',
        'page_count',
        'dimensions',
        'cover_image_url',
        'description',
        'publisher_id',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'page_count' => 'integer',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'author_book');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    /**
     * URL hiển thị ảnh bìa: link ngoài giữ nguyên, file upload lấy từ storage, không có thì null.
     */
    protected function coverUrl(): Attribute
    {
        return Attribute::get(function (): ?string {
            $path = $this->cover_image_url;

            if (! $path) {
                return null;
            }

            return Str::startsWith($path, ['http://', 'https://'])
                ? $path
                : Storage::disk('public')->url($path);
        });
    }

    /**
     * Xóa file ảnh bìa đã upload (bỏ qua link ngoài).
     */
    public function deleteCoverFile(): void
    {
        $path = $this->cover_image_url;

        if ($path && ! Str::startsWith($path, ['http://', 'https://'])) {
            Storage::disk('public')->delete($path);
        }
    }
}
