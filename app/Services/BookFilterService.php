<?php

namespace App\Services;

use App\Models\Book;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookFilterService
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function paginate(array $filters): LengthAwarePaginator
    {
        return Book::query()
            ->with(['authors', 'categories', 'publisher'])
            ->filter($filters)
            ->paginate(10)
            ->withQueryString();
    }
}
