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
     * Lọc sách theo từ khóa (tên, ISBN, tác giả), thể loại và tình trạng kho; giá trị không hợp lệ được bỏ qua.
     *
     * @param  array{keyword?: mixed, category_id?: mixed, stock?: mixed}  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $keyword = is_string($filters['keyword'] ?? null) ? trim($filters['keyword']) : '';
        $categoryId = filter_var($filters['category_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        $query
            ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                $query->where(function (Builder $query) use ($keyword): void {
                    $query->whereLike('title', "%{$keyword}%")
                        ->orWhereLike('isbn', '%'.preg_replace('/[\s-]+/', '', $keyword).'%')
                        ->orWhereHas('authors', fn (Builder $query) => $query->whereLike('name', "%{$keyword}%"));
                });
            })
            ->when($categoryId !== false, fn (Builder $query) => $query->whereHas(
                'categories',
                fn (Builder $query) => $query->whereKey($categoryId),
            ))
            ->when($filters['stock'] ?? null, fn (Builder $query, mixed $stock) => match ($stock) {
                'out' => $query->where('stock_quantity', 0),
                'low' => $query->whereBetween('stock_quantity', [1, self::LOW_STOCK - 1]),
                'in' => $query->where('stock_quantity', '>=', self::LOW_STOCK),
                default => $query,
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
