<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Warehouse extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
    ];

    public function books(): BelongsToMany
    {
        return $this->belongsToMany(Book::class, 'book_warehouse')->withPivot('quantity');
    }
}
