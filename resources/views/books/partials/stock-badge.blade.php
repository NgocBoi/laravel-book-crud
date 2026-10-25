{{-- Badge tồn kho: nhận $book (đã load warehouses). Tổng tồn = tổng quantity các kho --}}
@php $stock = (int) $book->warehouses->sum('pivot.quantity'); @endphp
@if ($stock === 0)
    <span class="badge text-bg-danger">Hết hàng</span>
@elseif ($stock < 5)
    <span class="badge text-bg-warning">Sắp hết ({{ $stock }})</span>
@else
    <span class="badge text-bg-success">{{ $stock }}</span>
@endif
