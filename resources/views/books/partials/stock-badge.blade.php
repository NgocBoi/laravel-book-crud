<<<<<<< HEAD
{{-- Badge tồn kho: nhận $book (đã load warehouses). Tổng tồn = tổng quantity các kho --}}
@php $stock = (int) $book->warehouses->sum('pivot.quantity'); @endphp
@if ($stock === 0)
    <span class="badge text-bg-danger">Hết hàng</span>
@elseif ($stock < 5)
    <span class="badge text-bg-warning">Sắp hết ({{ $stock }})</span>
@else
    <span class="badge text-bg-success">{{ $stock }}</span>
=======
{{-- Chỉ hiển thị trạng thái từ số lượng thật, không lưu thêm trạng thái trong CSDL. --}}
@php $stock = (int) $book->stock_quantity; @endphp
@if($stock === 0)
    <span class="inline-state out" title="Số lượng tồn kho bằng 0"><i class="bi bi-x-circle" aria-hidden="true"></i> Hết hàng</span>
@elseif($stock < \App\Models\Book::LOW_STOCK)
    <span class="inline-state low" title="Số lượng dưới ngưỡng {{ \App\Models\Book::LOW_STOCK }}"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> Sắp hết · {{ $stock }}</span>
@else
    <span class="inline-state available"><i class="bi bi-check-circle" aria-hidden="true"></i> Còn {{ $stock }}</span>
>>>>>>> ae060f5667446390cab464b2e187e2770374d4ad
@endif
