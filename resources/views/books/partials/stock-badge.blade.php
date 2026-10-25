{{-- Badge tồn kho: nhận $book. Hết hàng (= 0), Sắp hết (< Book::LOW_STOCK) --}}
@php $stock = (int) $book->stock_quantity; @endphp
@if ($stock === 0)
    <span class="badge text-bg-danger">Hết hàng</span>
@elseif ($stock < \App\Models\Book::LOW_STOCK)
    <span class="badge text-bg-warning">Sắp hết ({{ $stock }})</span>
@else
    <span class="badge text-bg-success">{{ $stock }}</span>
@endif
