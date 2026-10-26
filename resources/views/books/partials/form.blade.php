@php
    $editing = $mode === 'edit';
    $currentBook = $editing ? $book : null;
    $hasOldInput = session()->hasOldInput();
    $selectedAuthors = array_map('strval', (array) old('author_ids', $hasOldInput ? [] : ($currentBook?->authors->modelKeys() ?? [])));
    $selectedCategories = array_map('strval', (array) old('category_ids', $hasOldInput ? [] : ($currentBook?->categories->modelKeys() ?? [])));
    $authorError = $errors->first('author_ids') ?: $errors->first('author_ids.*');
    $categoryError = $errors->first('category_ids') ?: $errors->first('category_ids.*');
    $stockValue = old('stock_quantity', $currentBook?->stock_quantity ?? 0);
    $validStock = filter_var($stockValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 4294967295]]) !== false;
    $stock = $validStock ? (int) $stockValue : null;
    $stockState = $stock === 0 ? 'out' : ($stock < \App\Models\Book::LOW_STOCK ? 'low' : 'available');
@endphp

@push('styles')
    <link href="{{ asset('css/book-form.css') }}" rel="stylesheet">
@endpush

@if($errors->any())
    <div class="admin-alert alert-danger book-form-alert" role="alert" tabindex="-1" id="bookValidationAlert">
        <div>
            <h2 class="alert-heading"><span aria-hidden="true">⚠</span> Không thể {{ $editing ? 'cập nhật' : 'lưu' }} sách</h2>
            <p class="alert-detail">Vui lòng kiểm tra và bổ sung các trường có viền đỏ.</p>
        </div>
    </div>
@endif

<form action="{{ $editing ? route('books.update', $currentBook) : route('books.store') }}"
      method="POST" id="bookForm" class="book-editor" novalidate>
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <section class="section-card book-form-card" aria-labelledby="bookFormTitle">
        <h2 class="section-title" id="bookFormTitle">{{ $editing ? 'Cập nhật thông tin sách' : 'Thông tin sách' }}</h2>
        <p class="section-note book-form-note">Các trường có dấu * là bắt buộc.</p>

        <div class="book-form-grid">
            <div class="book-field">
                <label for="bookTitle" class="form-label">Tên sách <span aria-hidden="true">*</span></label>
                <input type="text" name="title" id="bookTitle" maxlength="255" required
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title', $currentBook?->title) }}" placeholder="Chọn hoặc nhập thông tin"
                       aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}" @error('title') aria-describedby="titleError" @enderror>
                @error('title')<p class="invalid-feedback" id="titleError">{{ $message }}</p>@enderror
            </div>

            <div class="book-field">
                <label for="bookIsbn" class="form-label">Mã sách / ISBN</label>
                <input type="text" name="isbn" id="bookIsbn" maxlength="20"
                       class="form-control @error('isbn') is-invalid @enderror"
                       value="{{ old('isbn', $currentBook?->isbn) }}" placeholder="Chọn hoặc nhập thông tin"
                       aria-invalid="{{ $errors->has('isbn') ? 'true' : 'false' }}" @error('isbn') aria-describedby="isbnError" @enderror>
                @error('isbn')<p class="invalid-feedback" id="isbnError">{{ $message }}</p>@enderror
            </div>

            <div class="book-field">
                <span class="form-label" id="authorsLabel">Tác giả <span aria-hidden="true">*</span></span>
                <details class="book-choice @if($authorError) is-invalid @endif" data-book-choice>
                    <summary id="authorsSummary" aria-labelledby="authorsLabel authorsValue" aria-describedby="authorsHint{{ $authorError ? ' authorsError' : '' }}" aria-invalid="{{ $authorError ? 'true' : 'false' }}">
                        <span id="authorsValue" data-choice-value data-placeholder="Chọn tác giả">{{ $authors->whereIn('id', $selectedAuthors)->pluck('name')->join(', ') ?: 'Chọn tác giả' }}</span>
                    </summary>
                    <div class="book-choice-options" role="group" aria-labelledby="authorsLabel">
                        <p class="book-choice-hint" id="authorsHint">Có thể chọn nhiều tác giả.</p>
                        @forelse($authors as $author)
                            <label class="book-choice-option" for="author-{{ $author->id }}">
                                <input type="checkbox" name="author_ids[]" id="author-{{ $author->id }}" value="{{ $author->id }}"
                                       class="form-check-input" @checked(in_array((string) $author->id, $selectedAuthors, true))>
                                <span>{{ $author->name }}</span>
                            </label>
                        @empty
                            <p class="book-choice-hint">Chưa có tác giả.</p>
                        @endforelse
                    </div>
                </details>
                @if($authorError)<p class="invalid-feedback" id="authorsError">{{ $authorError }}</p>@endif
            </div>

            <div class="book-field">
                <span class="form-label" id="categoriesLabel">Thể loại <span aria-hidden="true">*</span></span>
                <details class="book-choice @if($categoryError) is-invalid @endif" data-book-choice>
                    <summary id="categoriesSummary" aria-labelledby="categoriesLabel categoriesValue" aria-describedby="categoriesHint{{ $categoryError ? ' categoriesError' : '' }}" aria-invalid="{{ $categoryError ? 'true' : 'false' }}">
                        <span id="categoriesValue" data-choice-value data-placeholder="Chọn thể loại">{{ $categories->whereIn('id', $selectedCategories)->pluck('name')->join(', ') ?: 'Chọn thể loại' }}</span>
                    </summary>
                    <div class="book-choice-options" role="group" aria-labelledby="categoriesLabel">
                        <p class="book-choice-hint" id="categoriesHint">Có thể chọn nhiều thể loại.</p>
                        @forelse($categories as $category)
                            <label class="book-choice-option" for="category-{{ $category->id }}">
                                <input type="checkbox" name="category_ids[]" id="category-{{ $category->id }}" value="{{ $category->id }}"
                                       class="form-check-input" @checked(in_array((string) $category->id, $selectedCategories, true))>
                                <span>{{ $category->name }}</span>
                            </label>
                        @empty
                            <p class="book-choice-hint">Chưa có thể loại.</p>
                        @endforelse
                    </div>
                </details>
                @if($categoryError)<p class="invalid-feedback" id="categoriesError">{{ $categoryError }}</p>@endif
            </div>

            <div class="book-field">
                <label for="publisherId" class="form-label">Nhà xuất bản</label>
                <select name="publisher_id" id="publisherId" class="form-select @error('publisher_id') is-invalid @enderror"
                        aria-invalid="{{ $errors->has('publisher_id') ? 'true' : 'false' }}" @error('publisher_id') aria-describedby="publisherError" @enderror>
                    <option value="">Chọn nhà xuất bản</option>
                    @foreach($publishers as $publisher)
                        <option value="{{ $publisher->id }}" @selected((string) old('publisher_id', $currentBook?->publisher_id) === (string) $publisher->id)>{{ $publisher->name }}</option>
                    @endforeach
                </select>
                @error('publisher_id')<p class="invalid-feedback" id="publisherError">{{ $message }}</p>@enderror
            </div>

            <div class="book-field">
                <label for="publishedYear" class="form-label">Năm xuất bản</label>
                <input type="number" name="published_year" id="publishedYear" min="1901" max="2155" step="1"
                       class="form-control @error('published_year') is-invalid @enderror"
                       value="{{ old('published_year', $currentBook?->published_year) }}" placeholder="Chọn hoặc nhập thông tin"
                       aria-invalid="{{ $errors->has('published_year') ? 'true' : 'false' }}" @error('published_year') aria-describedby="yearError" @enderror>
                @error('published_year')<p class="invalid-feedback" id="yearError">{{ $message }}</p>@enderror
            </div>

            <div class="book-field">
                <label for="price" class="form-label">Giá bán (VNĐ) <span aria-hidden="true">*</span></label>
                <input type="number" name="price" id="price" min="0" max="9999999999.99" step="0.01" required
                       class="form-control @error('price') is-invalid @enderror"
                       value="{{ old('price', $currentBook?->price ?? 0) }}" placeholder="Chọn hoặc nhập thông tin"
                       aria-invalid="{{ $errors->has('price') ? 'true' : 'false' }}" @error('price') aria-describedby="priceError" @enderror>
                @error('price')<p class="invalid-feedback" id="priceError">{{ $message }}</p>@enderror
            </div>

            <div class="book-field">
                <label for="stockQuantity" class="form-label">Số lượng tồn kho <span aria-hidden="true">*</span></label>
                <input type="number" name="stock_quantity" id="stockQuantity" min="0" max="4294967295" step="1" required
                       class="form-control @error('stock_quantity') is-invalid @enderror"
                       value="{{ $stockValue }}" placeholder="Chọn hoặc nhập thông tin" data-low-stock="{{ \App\Models\Book::LOW_STOCK }}"
                       aria-invalid="{{ $errors->has('stock_quantity') ? 'true' : 'false' }}" aria-describedby="stockHint{{ $errors->has('stock_quantity') ? ' stockError' : '' }}">
                @error('stock_quantity')<p class="invalid-feedback" id="stockError">{{ $message }}</p>@enderror
                <div class="book-stock-feedback">
                    <span id="stockStatus" class="inline-state {{ $stockState }} {{ $validStock ? '' : 'd-none' }}" role="status" aria-live="polite" aria-atomic="true">
                        {{ $stock === 0 ? 'Hết hàng' : ($stock < \App\Models\Book::LOW_STOCK ? 'Sắp hết hàng' : 'Còn hàng') }}
                    </span>
                    <span class="form-hint" id="stockHint">0: Hết hàng · 1–{{ \App\Models\Book::LOW_STOCK - 1 }}: Sắp hết</span>
                </div>
            </div>

            <div class="book-field book-field-wide">
                <label for="description" class="form-label">Mô tả sách</label>
                <textarea name="description" id="description" rows="1" maxlength="10000"
                          class="form-control @error('description') is-invalid @enderror" placeholder="Nhập nội dung mô tả..."
                          aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}" @error('description') aria-describedby="descriptionError" @enderror>{{ old('description', $currentBook?->description) }}</textarea>
                @error('description')<p class="invalid-feedback" id="descriptionError">{{ $message }}</p>@enderror
            </div>
        </div>

        <footer class="book-form-actions">
            <a href="{{ route('books.index') }}" class="btn btn-outline-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary">{{ $editing ? 'Cập nhật sách' : 'Lưu sách' }}</button>
        </footer>
    </section>
</form>

@push('scripts')
    <script src="{{ asset('js/book-form.js') }}" defer></script>
@endpush
