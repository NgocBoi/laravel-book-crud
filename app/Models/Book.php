<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'author_id',
        'publisher_id',
        'book_code',
        'isbn',
        'title',
        'translator',
        'supplier',
        'publish_year',
        'language',
        'weight',
        'dimensions',
        'pages',
        'original_price',
        'price',
        'stock_quantity',
        'sold_quantity',
        'cover_image',
        'description',
        'is_trending',
        'badge_type',
    ];

    protected $casts = [
        'publish_year' => 'integer',
        'weight' => 'integer',
        'pages' => 'integer',
        'original_price' => 'decimal:2',
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'sold_quantity' => 'integer',
        'is_trending' => 'boolean',
    ];

    /**
     * Relationship: Book belongs to Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship: Book belongs to Author.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Relationship: Book belongs to Publisher.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    /**
     * Accessor: Alias 'stock' to 'stock_quantity'.
     */
    public function getStockAttribute(): int
    {
        return (int) $this->stock_quantity;
    }

    public function setStockAttribute($value): void
    {
        $this->attributes['stock_quantity'] = (int) $value;
    }

    /**
     * Accessor: Alias 'quantity' to 'stock_quantity'.
     */
    public function getQuantityAttribute(): int
    {
        return (int) $this->stock_quantity;
    }

    public function setQuantityAttribute($value): void
    {
        $this->attributes['stock_quantity'] = (int) $value;
    }

    /**
     * Convenient name accessors for views.
     */
    public function getAuthorNameAttribute(): string
    {
        return $this->author?->name ?? 'Không rõ';
    }

    public function getPublisherNameAttribute(): string
    {
        return $this->publisher?->name ?? 'Không rõ';
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->category?->name ?? 'Không rõ';
    }

    /**
     * Helper to get cover image URL.
     */
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image && file_exists(public_path('storage/books/' . $this->cover_image))) {
            return asset('storage/books/' . $this->cover_image);
        }
        if ($this->cover_image && file_exists(public_path('books/' . $this->cover_image))) {
            return asset('books/' . $this->cover_image);
        }
        return 'https://via.placeholder.com/300x420?text=No+Cover';
    }
}
