<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'isbn',
        'title',
        'published_year',
        'price',
        'page_count',
        'dimensions',
        'cover_image_url',
        'description',
        'publisher_id',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'price' => 'decimal:2',
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

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'book_warehouse')->withPivot('quantity');
    }
}
