{{-- Ba tham số lọc được xử lý bởi Book::scopeFilter (dùng chung cho danh sách và xuất Excel). --}}
<form action="{{ route('books.index') }}" method="GET" class="section-card mb-4" role="search">
    <div class="section-card-body">
        <div class="search-head">
            <div>
                <h2 class="section-title mb-1">Tìm kiếm &amp; bộ lọc</h2>
                <p class="section-note mb-0">Tra cứu theo tên sách, ISBN, tác giả, thể loại và tồn kho.</p>
            </div>
            @if(request()->filled('keyword') || request()->filled('category_id') || request()->filled('stock'))
                <a href="{{ route('books.index') }}" class="small text-decoration-none"><i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại bộ lọc</a>
            @endif
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-12 col-lg-5">
                <label for="searchKeyword" class="admin-filter-label">Từ khóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                    <input id="searchKeyword" name="keyword" class="form-control border-start-0" type="search" maxlength="255"
                           placeholder="Tên sách, tác giả, ISBN..." value="{{ is_string(request('keyword')) ? request('keyword') : '' }}">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="categoryFilter" class="admin-filter-label">Thể loại</label>
                <select name="category_id" id="categoryFilter" class="form-select">
                    <option value="">Tất cả thể loại</option>
                    @foreach(($categories ?? collect()) as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id && is_scalar(request('category_id')))>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label for="stockFilter" class="admin-filter-label">Tình trạng kho</label>
                <select name="stock" id="stockFilter" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="in" @selected(request('stock') === 'in')>Còn hàng</option>
                    <option value="low" @selected(request('stock') === 'low')>Sắp hết</option>
                    <option value="out" @selected(request('stock') === 'out')>Hết hàng</option>
                </select>
            </div>
            <div class="col-12 col-lg-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Tìm kiếm</button>
            </div>
        </div>
    </div>
</form>
