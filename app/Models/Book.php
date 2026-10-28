<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Book $book): void {
            if ($book->getAttribute('created_at') === null) {
                $book->setAttribute('created_at', now());
            }
        });
    }

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
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $minPrice = $filters['min_price'] ?? null;
        $maxPrice = $filters['max_price'] ?? null;

        return $query
            ->when($filters['keyword'] ?? null, function (Builder $query, string $keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('isbn', 'like', "%{$keyword}%")
                        ->orWhereHas('authors', fn (Builder $authors) => $authors->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($filters['category_id'] ?? null, function (Builder $query, int|string $categoryId): void {
                $query->whereHas('categories', fn (Builder $categories) => $categories->whereKey($categoryId));
            })
            ->when($filters['author_id'] ?? null, function (Builder $query, int|string $authorId): void {
                $query->whereHas('authors', fn (Builder $authors) => $authors->whereKey($authorId));
            })
            ->when($filters['publisher_id'] ?? null, function (Builder $query, int|string $publisherId): void {
                $query->where('publisher_id', $publisherId);
            })
            ->when($minPrice !== null && $minPrice !== '', function (Builder $query) use ($minPrice): void {
                $query->where('price', '>=', $minPrice);
            })
            ->when($maxPrice !== null && $maxPrice !== '', function (Builder $query) use ($maxPrice): void {
                $query->where('price', '<=', $maxPrice);
            })
            ->when($filters['stock'] ?? null, function (Builder $query, string $stock): void {
                match ($stock) {
                    'in' => $query->where('stock_quantity', '>', self::LOW_STOCK),
                    'low' => $query->whereBetween('stock_quantity', [1, self::LOW_STOCK]),
                    'out' => $query->where('stock_quantity', 0),
                };
            })
            ->when($filters['sort'] ?? 'newest', function (Builder $query, string $sort): void {
                match ($sort) {
                    'price_asc' => $query->orderBy('price')->orderByDesc('id'),
                    'price_desc' => $query->orderByDesc('price')->orderByDesc('id'),
                    'oldest' => $query
                        ->orderByRaw('CASE WHEN created_at IS NULL THEN 0 ELSE 1 END')
                        ->orderBy('created_at')
                        ->orderBy('id'),
                    default => $query
                        ->orderByRaw('CASE WHEN created_at IS NULL THEN 1 ELSE 0 END')
                        ->orderByDesc('created_at')
                        ->orderByDesc('id'),
                };
            });
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
