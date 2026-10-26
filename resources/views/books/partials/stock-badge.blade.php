{{-- Chỉ hiển thị trạng thái từ số lượng thật, không lưu thêm trạng thái trong CSDL. --}}
@php $stock = (int) $book->stock_quantity; @endphp
@if($stock === 0)
    <span class="inline-state out" title="Số lượng tồn kho bằng 0"><i class="bi bi-x-circle" aria-hidden="true"></i> Hết hàng</span>
@elseif($stock < \App\Models\Book::LOW_STOCK)
    <span class="inline-state low" title="Số lượng dưới ngưỡng {{ \App\Models\Book::LOW_STOCK }}"><i class="bi bi-exclamation-circle" aria-hidden="true"></i> Sắp hết · {{ $stock }}</span>
@else
    <span class="inline-state available"><i class="bi bi-check-circle" aria-hidden="true"></i> Còn {{ $stock }}</span>
@endif
