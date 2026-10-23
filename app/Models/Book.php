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
        'book_code',
        'isbn',
        'title',
        'author',
        'translator',
        'publisher',
        'supplier',
        'publish_year',
        'language',
        'weight',
        'dimensions',
        'pages',
        'original_price',
        'price',
        'stock',
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
        'stock' => 'integer',
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
     * Accessor: Alias 'quantity' to 'stock' for backward/cross-member compatibility.
     */
    public function getQuantityAttribute(): int
    {
        return (int) $this->stock;
    }

    public function setQuantityAttribute($value): void
    {
        $this->attributes['stock'] = (int) $value;
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
