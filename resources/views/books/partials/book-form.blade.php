{{-- Các trường dùng chung cho form thêm/sửa sách. $book là null khi thêm mới --}}
@php
    $book = $book ?? null;
    $selectedAuthors = old('author_ids', $book?->authors->pluck('id')->all() ?? []);
    $selectedCategories = old('category_ids', $book?->categories->pluck('id')->all() ?? []);
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label for="isbn" class="form-label">Mã sách (ISBN)</label>
        <input type="text" id="isbn" name="isbn" maxlength="20"
               value="{{ old('isbn', $book?->isbn) }}"
               class="form-control @error('isbn') is-invalid @enderror"
               @readonly($book)>
        @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-8">
        <label for="title" class="form-label">Tên sách <span class="text-danger">*</span></label>
        <input type="text" id="title" name="title" maxlength="255" required
               value="{{ old('title', $book?->title) }}"
               class="form-control @error('title') is-invalid @enderror">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
        <input type="number" id="price" name="price" min="0" step="1000" required
               value="{{ old('price', $book?->price) }}"
               class="form-control @error('price') is-invalid @enderror">
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="published_year" class="form-label">Năm xuất bản</label>
        <input type="number" id="published_year" name="published_year" min="1000" max="{{ date('Y') }}"
               value="{{ old('published_year', $book?->published_year) }}"
               class="form-control @error('published_year') is-invalid @enderror">
        @error('published_year') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="page_count" class="form-label">Số trang</label>
        <input type="number" id="page_count" name="page_count" min="1"
               value="{{ old('page_count', $book?->page_count) }}"
               class="form-control @error('page_count') is-invalid @enderror">
        @error('page_count') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="dimensions" class="form-label">Kích thước</label>
        <input type="text" id="dimensions" name="dimensions" maxlength="50" placeholder="14 x 20 cm"
               value="{{ old('dimensions', $book?->dimensions) }}"
               class="form-control @error('dimensions') is-invalid @enderror">
        @error('dimensions') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="publisher_id" class="form-label">Nhà xuất bản</label>
        <select id="publisher_id" name="publisher_id" class="form-select @error('publisher_id') is-invalid @enderror">
            <option value="">-- Chọn NXB --</option>
            @foreach ($publishers as $publisher)
                <option value="{{ $publisher->id }}" @selected(old('publisher_id', $book?->publisher_id) == $publisher->id)>
                    {{ $publisher->name }}
                </option>
            @endforeach
        </select>
        @error('publisher_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="author_ids" class="form-label">Tác giả</label>
        <select id="author_ids" name="author_ids[]" multiple size="5"
                class="form-select @if ($errors->has('author_ids') || $errors->has('author_ids.*')) is-invalid @endif">
            @foreach ($authors as $author)
                <option value="{{ $author->id }}" @selected(in_array($author->id, $selectedAuthors))>{{ $author->name }}</option>
            @endforeach
        </select>
        <div class="form-text">Giữ Ctrl để chọn nhiều.</div>
        @if ($errors->has('author_ids') || $errors->has('author_ids.*'))
            <div class="invalid-feedback">{{ $errors->first('author_ids') ?: $errors->first('author_ids.*') }}</div>
        @endif
    </div>

    <div class="col-md-4">
        <label for="category_ids" class="form-label">Thể loại</label>
        <select id="category_ids" name="category_ids[]" multiple size="5"
                class="form-select @if ($errors->has('category_ids') || $errors->has('category_ids.*')) is-invalid @endif">
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(in_array($category->id, $selectedCategories))>{{ $category->name }}</option>
            @endforeach
        </select>
        <div class="form-text">Giữ Ctrl để chọn nhiều.</div>
        @if ($errors->has('category_ids') || $errors->has('category_ids.*'))
            <div class="invalid-feedback">{{ $errors->first('category_ids') ?: $errors->first('category_ids.*') }}</div>
        @endif
    </div>

    <div class="col-12">
        <label for="cover_image" class="form-label">Ảnh bìa</label>
        @if ($book?->cover_image_url)
            <div class="mb-2" style="max-width: 120px;">
                @include('books.partials.book-cover', ['book' => $book, 'class' => 'img-thumbnail'])
                <div class="form-text">Chọn ảnh mới để thay ảnh hiện tại.</div>
            </div>
        @endif
        <input type="file" id="cover_image" name="cover_image" accept="image/*"
               class="form-control @error('cover_image') is-invalid @enderror">
        @error('cover_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Mô tả</label>
        <textarea id="description" name="description" rows="4"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $book?->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>
