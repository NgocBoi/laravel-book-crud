{{-- Modal xem nhanh dùng dữ liệu từ nút mở trên mỗi hàng; không truy vấn hay thay đổi backend. --}}
<div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="qv-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header px-4 py-3">
                <div><div class="eyebrow mb-1">Xem nhanh</div><h2 class="modal-title fs-5 fw-bold" id="qv-title"></h2></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="row g-4">
                    <div class="col-sm-4">
                        <img id="qv-cover" class="img-fluid rounded-3 border d-none" alt="Ảnh bìa sách" style="width:100%;max-height:240px;object-fit:contain">
                        <div id="qv-no-cover" class="book-image-empty"><i class="bi bi-book"></i><span>Chưa có ảnh bìa</span></div>
                    </div>
                    <div class="col-sm-8">
                        <dl class="row mb-0 g-2">
                            <dt class="col-4 text-secondary fw-medium">Tác giả</dt><dd class="col-8 fw-semibold" id="qv-authors"></dd>
                            <dt class="col-4 text-secondary fw-medium">Thể loại</dt><dd class="col-8" id="qv-categories"></dd>
                            <dt class="col-4 text-secondary fw-medium">Nhà xuất bản</dt><dd class="col-8" id="qv-publisher"></dd>
                            <dt class="col-4 text-secondary fw-medium">Giá bán</dt><dd class="col-8 fw-bold text-primary" id="qv-price"></dd>
                            <dt class="col-4 text-secondary fw-medium">Tồn kho</dt><dd class="col-8"><span id="qv-stock-badge" class="inline-state"></span></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3">
                <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Đóng</button>
                <a href="#" id="qv-link" class="btn btn-primary"><i class="bi bi-box-arrow-up-right me-1"></i> Xem chi tiết</a>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
/* Chỉ cập nhật nội dung modal và màu badge, không ghi dữ liệu. */
document.getElementById('quickViewModal')?.addEventListener('show.bs.modal', function (event) {
    const trigger = event.relatedTarget;
    if (!trigger) return;
    const d = trigger.dataset;
    const setText = (id, value) => { document.getElementById(id).textContent = value || '—'; };
    setText('qv-title', d.title);
    setText('qv-authors', d.authors);
    setText('qv-categories', d.categories);
    setText('qv-publisher', d.publisher);
    setText('qv-price', d.price);
    document.getElementById('qv-link').href = d.url || '#';
    const cover = document.getElementById('qv-cover');
    const hasCover = Boolean(d.cover);
    if (hasCover) cover.src = d.cover;
    else cover.removeAttribute('src');
    cover.classList.toggle('d-none', !hasCover);
    document.getElementById('qv-no-cover').classList.toggle('d-none', hasCover);
    const stock = Number.parseInt(d.stock, 10);
    const badge = document.getElementById('qv-stock-badge');
    badge.className = 'inline-state ' + (stock === 0 ? 'out' : stock < {{ \App\Models\Book::LOW_STOCK }} ? 'low' : 'available');
    badge.textContent = stock === 0 ? 'Hết hàng' : stock < {{ \App\Models\Book::LOW_STOCK }} ? 'Sắp hết · ' + stock : 'Còn ' + stock;
});
</script>
@endpush
