<form action="{{ route('books.index') }}" method="GET" class="section-card mb-4" role="search">
    <div class="section-card-body">
        <div class="search-head">
            <div>
                <h2 class="section-title mb-1">Tìm kiếm &amp; bộ lọc</h2>
                <p class="section-note mb-0">Lọc sách theo thể loại, tác giả, nhà xuất bản, giá và tình trạng kho.</p>
            </div>
            @if(request()->hasAny(['keyword', 'category_id', 'author_id', 'publisher_id', 'min_price', 'max_price', 'stock', 'sort']))
                <a href="{{ route('books.index') }}" class="small text-decoration-none"><i class="bi bi-arrow-counterclockwise me-1"></i> Đặt lại bộ lọc</a>
            @endif
        </div>
        <div class="row g-3 align-items-end">
            <div class="col-12 col-md-6 col-lg-4">
                <label for="searchKeyword" class="admin-filter-label">Từ khóa</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-secondary"><i class="bi bi-search"></i></span>
                    <input id="searchKeyword" name="keyword" class="form-control border-start-0" type="search" maxlength="255"
                           placeholder="Tên sách, tác giả, ISBN..." value="{{ request('keyword') }}">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label for="categoryFilter" class="admin-filter-label">Thể loại</label>
                <select name="category_id" id="categoryFilter" class="form-select">
                    <option value="">Tất cả thể loại</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label for="authorFilter" class="admin-filter-label">Tác giả</label>
                <select name="author_id" id="authorFilter" class="form-select">
                    <option value="">Tất cả tác giả</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}" @selected((string) request('author_id') === (string) $author->id)>{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-2">
                <label for="publisherFilter" class="admin-filter-label">Nhà xuất bản</label>
                <select name="publisher_id" id="publisherFilter" class="form-select">
                    <option value="">Tất cả nhà xuất bản</option>
                    @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" @selected((string) request('publisher_id') === (string) $publisher->id)>{{ $publisher->name }}</option>
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
            <div class="col-6 col-md-3 col-lg-2">
                <label for="minPriceFilter" class="admin-filter-label">Giá từ</label>
                <input type="number" name="min_price" id="minPriceFilter" class="form-control" min="0" step="0.01"
                       value="{{ request('min_price') }}" placeholder="Giá thấp nhất">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <label for="maxPriceFilter" class="admin-filter-label">Giá đến</label>
                <input type="number" name="max_price" id="maxPriceFilter" class="form-control" min="0" step="0.01"
                       value="{{ request('max_price') }}" placeholder="Giá cao nhất">
            </div>
            <div class="col-12 col-md-4 col-lg-3">
                <label for="sortFilter" class="admin-filter-label">Sắp xếp</label>
                <select name="sort" id="sortFilter" class="form-select">
                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>Mới thêm</option>
                    <option value="oldest" @selected(request('sort') === 'oldest')>Cũ nhất</option>
                    <option value="price_asc" @selected(request('sort') === 'price_asc')>Giá tăng dần</option>
                    <option value="price_desc" @selected(request('sort') === 'price_desc')>Giá giảm dần</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i> Lọc sách</button>
            </div>
        </div>
    </div>
</form>
