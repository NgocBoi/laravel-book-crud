# Validation & Upload ảnh bìa sách

Người phụ trách: **Phan Hoàng Dinh** · Nhánh: `hoangdinh`

## Các file

| File | Vai trò |
|---|---|
| `app/Http/Requests/StoreBookRequest.php` | Kiểm tra dữ liệu form **thêm** sách |
| `app/Http/Requests/UpdateBookRequest.php` | Kiểm tra dữ liệu form **sửa** sách (kế thừa StoreBookRequest, ISBN chỉ đọc) |
| `app/Services/BookCoverService.php` | Lưu / xóa / lấy link ảnh bìa trong `storage/app/public/covers` |
| `tests/Feature/BookRequestValidationTest.php`, `BookCoverServiceTest.php` | Test tự động: `php artisan test --filter="BookRequest\|BookCover"` |

## Quy tắc kiểm tra

| Trường (`name` trong form) | Quy tắc |
|---|---|
| `title` | Bắt buộc, tối đa 255 ký tự |
| `isbn` | Không bắt buộc, tối đa 20 ký tự, không trùng; tự bỏ dấu `-` và khoảng trắng. **Không sửa được khi cập nhật** |
| `price` | Bắt buộc, là số, ≥ 0 |
| `published_year` | Số nguyên 1901 – năm sau |
| `page_count` | Số nguyên ≥ 1 |
| `dimensions` | Tối đa 50 ký tự |
| `description` | Không bắt buộc |
| `publisher_id` | Phải tồn tại trong `publishers` |
| `author_ids[]` | Bắt buộc ≥ 1, không trùng, phải tồn tại trong `authors` (ô để trống được bỏ qua) |
| `category_ids[]` | Bắt buộc ≥ 1, không trùng, phải tồn tại trong `categories` (ô để trống được bỏ qua) |
| `cover_image` | Không bắt buộc; jpg / jpeg / png / webp, tối đa 2MB |

Thông báo lỗi tiếng Việt nằm trong `messages()` / `attributes()` của Request.

## Ghép vào hệ thống

**Controller sách** (`BookController`):

```php
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Services\BookCoverService;

public function store(StoreBookRequest $request, BookCoverService $covers)
{
    $data = $request->bookData();
    $newCover = $covers->store($request->file('cover_image'));
    if ($newCover) {
        $data['cover_image_url'] = $newCover;
    }

    try {
        $book = DB::transaction(function () use ($data, $request) {
            $book = Book::create($data);
            $book->authors()->sync($request->authorIds());
            $book->categories()->sync($request->categoryIds());

            return $book;
        });
    } catch (Throwable $e) {
        $covers->delete($newCover);   // lưu lỗi -> không để lại file rác
        throw $e;
    }

    return redirect()->route('books.show', $book)->with('success', 'Đã thêm sách.');
}

public function update(UpdateBookRequest $request, Book $book, BookCoverService $covers)
{
    $oldCover = $book->cover_image_url;
    // ... giống store(), dùng $book->update($data) ...
    if ($newCover) {
        $covers->delete($oldCover);   // chỉ xóa ảnh cũ khi đã lưu xong ảnh mới
    }
}
```

**Thùng rác** — khi xóa vĩnh viễn: `$covers->delete($book->cover_image_url);` (xóa mềm thì **giữ** ảnh để còn khôi phục).

**Giao diện form**: thẻ `<form>` phải có `enctype="multipart/form-data"`; tên ô đúng như bảng trên; hiện lỗi bằng `@error('title') ... @enderror`.

**Hiển thị ảnh**: `app(\App\Services\BookCoverService::class)->url($book->cover_image_url)`.

**Mỗi máy chạy một lần**: `php artisan storage:link`.

## Khi CSDL có thêm cột tồn kho

Nếu bảng `books` thêm cột `stock_quantity`, chỉ cần thêm vào `rules()` của StoreBookRequest:

```php
'stock_quantity' => ['required', 'integer', 'min:0'],
```

và `'stock_quantity' => 'số lượng tồn'` vào `attributes()`.
