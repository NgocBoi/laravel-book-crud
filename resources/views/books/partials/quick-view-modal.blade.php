{{-- Modal xem nhanh sách, dữ liệu lấy từ data-* của nút .btn-quick-view --}}
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qv-title"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-4">
                        <img id="qv-cover" class="img-fluid rounded d-none" alt="">
                        <div id="qv-no-cover" class="rounded bg-secondary-subtle d-flex align-items-center justify-content-center text-secondary" style="height:160px"><i class="bi bi-book fs-1"></i></div>
                    </div>
                    <div class="col-8">
                        <dl class="row mb-0 small">
                            <dt class="col-5">Tác giả</dt><dd class="col-7" id="qv-authors"></dd>
                            <dt class="col-5">Thể loại</dt><dd class="col-7" id="qv-categories"></dd>
                            <dt class="col-5">NXB</dt><dd class="col-7" id="qv-publisher"></dd>
                            <dt class="col-5">Giá</dt><dd class="col-7" id="qv-price"></dd>
                            <dt class="col-5">Tồn kho</dt><dd class="col-7" id="qv-stock"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="qv-link" class="btn btn-primary btn-sm">Xem chi tiết</a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('quickViewModal').addEventListener('show.bs.modal', function (event) {
    const d = event.relatedTarget.dataset;
    document.getElementById('qv-title').textContent = d.title;
    document.getElementById('qv-authors').textContent = d.authors;
    document.getElementById('qv-categories').textContent = d.categories;
    document.getElementById('qv-publisher').textContent = d.publisher;
    document.getElementById('qv-price').textContent = d.price;
    document.getElementById('qv-stock').textContent = d.stock;
    document.getElementById('qv-link').href = d.url;

    const cover = document.getElementById('qv-cover');
    cover.src = d.cover || '';
    cover.classList.toggle('d-none', !d.cover);
    document.getElementById('qv-no-cover').classList.toggle('d-none', !!d.cover);
});
</script>
