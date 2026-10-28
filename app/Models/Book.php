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

    /** Các tham số query được Book::scopeFilter xử lý. */
    public const FILTER_KEYS = [
        'keyword',
        'category_id',
        'author_id',
        'publisher_id',
        'stock',
        'price_min',
        'price_max',
        'min_price',
        'max_price',
        'sort',
    ];

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
     * Lọc sách theo từ khóa (tên, ISBN, tác giả), thể loại, tác giả, nhà xuất bản, tình trạng kho, khoảng giá và sắp xếp.
     *
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        $keyword = is_string($filters['keyword'] ?? null) ? trim($filters['keyword']) : '';
        $categoryId = filter_var($filters['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $authorId = filter_var($filters['author_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $publisherId = filter_var($filters['publisher_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        $rawMinPrice = $filters['min_price'] ?? $filters['price_min'] ?? null;
        $rawMaxPrice = $filters['max_price'] ?? $filters['price_max'] ?? null;

        $minPrice = ($rawMinPrice !== null && $rawMinPrice !== '')
            ? filter_var($rawMinPrice, FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]])
            : false;
        $maxPrice = ($rawMaxPrice !== null && $rawMaxPrice !== '')
            ? filter_var($rawMaxPrice, FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]])
            : false;

        $stock = is_string($filters['stock'] ?? null) ? $filters['stock'] : null;
        $sort = is_string($filters['sort'] ?? null) ? $filters['sort'] : 'newest';

        return $query
            ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->where('title', 'like', "%{$keyword}%")
                        ->orWhere('isbn', 'like', '%'.preg_replace('/[\s-]+/', '', $keyword).'%')
                        ->orWhereHas('authors', fn (Builder $authors) => $authors->where('name', 'like', "%{$keyword}%"));
                });
            })
            ->when($categoryId !== false && $categoryId !== null, function (Builder $query) use ($categoryId): void {
                $query->whereHas('categories', fn (Builder $categories) => $categories->whereKey($categoryId));
            })
            ->when($authorId !== false && $authorId !== null, function (Builder $query) use ($authorId): void {
                $query->whereHas('authors', fn (Builder $authors) => $authors->whereKey($authorId));
            })
            ->when($publisherId !== false && $publisherId !== null, function (Builder $query) use ($publisherId): void {
                $query->where('publisher_id', $publisherId);
            })
            ->when($minPrice !== false, function (Builder $query) use ($minPrice): void {
                $query->where('price', '>=', $minPrice);
            })
            ->when($maxPrice !== false, function (Builder $query) use ($maxPrice): void {
                $query->where('price', '<=', $maxPrice);
            })
            ->when($stock !== null, function (Builder $query) use ($stock): void {
                match ($stock) {
                    'in' => $query->where('stock_quantity', '>=', self::LOW_STOCK),
                    'low' => $query->whereBetween('stock_quantity', [1, self::LOW_STOCK - 1]),
                    'out' => $query->where('stock_quantity', 0),
                    default => null,
                };
            })
            ->when(true, function (Builder $query) use ($sort): void {
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
