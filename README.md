# TÀI LIỆU HƯỚNG DẪN KỸ THUẬT: CÀI ĐẶT VÀ PHÁT TRIỂN ỨNG DỤNG WEB VỚI LARAVEL
## Bài thực hành: Quản lý đầu sách (CRUD) — Mô hình MVC

> **Học phần:** Phát triển ứng dụng web với mã nguồn mở  
> **Đơn vị:** Khoa Công nghệ thông tin — Trường Kỹ thuật và Công nghệ — Đại học Trà Vinh  
> **Lớp:** DA23TTD — **Nhóm thực hiện:** Nhóm 3  
> **Nền tảng kỹ thuật:** Laravel 13 (`^13.17`), PHP 8.4.x (`^8.3`), Composer 2.x, MySQL (XAMPP), Bootstrap 5.3.3

---

## Mục lục

1. [Giới thiệu và mục tiêu dự án](#1-giới-thiệu-và-mục-tiêu-dự-án)
   - 1.1. Phạm vi dự án
   - 1.2. Kết quả đầu ra yêu cầu
   - 1.3. Luồng xử lý chuẩn theo mô hình MVC
2. [Hướng dẫn thiết lập và khởi chạy dự án cho thành viên nhóm](#2-hướng-dẫn-thiết-lập-và-khởi-chạy-dự-án-cho-thành-viên-nhóm)
   - 2.1. Quy trình thiết lập nhanh
   - 2.2. Tài khoản kiểm thử hệ thống
   - 2.3. Chạy bộ kiểm thử tự động (PHPUnit)
3. [Chuẩn bị môi trường và kiểm tra cài đặt](#3-chuẩn-bị-môi-trường-và-kiểm-tra-cài-đặt)
   - 3.1. Bảng yêu cầu môi trường
   - 3.2. Kiểm tra phiên bản công cụ
   - 3.3. Cấu hình extension PHP 8.4
4. [Nhận diện cấu trúc thư mục của dự án](#4-nhận-diện-cấu-trúc-thư-mục-của-dự-án)
   - 4.1. Bảng vai trò các thư mục và tập tin cốt lõi
   - 4.2. Lộ trình đọc code nhanh cho thành viên mới
5. [Thiết kế cơ sở dữ liệu và cấu hình kết nối](#5-thiết-kế-cơ-sở-dữ-liệu-và-cấu-hình-kết-nối)
   - 5.1. Sơ đồ quan hệ thực thể (ERD 3NF)
   - 5.2. Đặc tả chi tiết các bảng dữ liệu
   - 5.3. Tạo cơ sở dữ liệu và cấu hình file `.env`
6. [Xây dựng chức năng CRUD theo mô hình MVC](#6-xây-dựng-chức-năng-crud-theo-mô-hình-mvc)
   - 6.1. Định tuyến (Routes)
   - 6.2. Bộ điều khiển (Controllers) và Form Requests
   - 6.3. Mô hình dữ liệu (Models & Eloquent Relationships)
   - 6.4. Giao diện người dùng (Blade Views & Bootstrap 5)
   - 6.5. Quy ước xử lý ảnh bìa và trạng thái tồn kho
   - 6.6. Bảng tra cứu vị trí từng chức năng trong mã nguồn
   - 6.7. Chỉ dẫn nhanh: Muốn sửa gì thì mở file nào?
7. [Phân công nhiệm vụ nhóm 8 thành viên](#7-phân-công-nhiệm-vụ-nhóm-8-thành-viên)
8. [Quy ước làm việc với Git của nhóm](#8-quy-ước-làm-việc-với-git-của-nhóm)
9. [Bảng tổng hợp các lỗi thực tế và cách xử lý](#9-bảng-tổng-hợp-các-lỗi-thực-tế-và-cách-xử-lý)
10. [Kết luận và hướng phát triển](#10-kết-luận-và-hướng-phát-triển)

---

## 1. Giới thiệu và mục tiêu dự án

### 1.1. Phạm vi dự án
Tài liệu hướng dẫn thiết lập môi trường phát triển, khởi tạo và cấu trúc dự án ứng dụng web với framework **Laravel 13 (PHP 8.4)**, kết nối hệ quản trị cơ sở dữ liệu **MySQL**, nhận diện vai trò các thành phần trong kiến trúc MVC và phát triển hoàn chỉnh chức năng CRUD (Create - Read - Update - Delete) cho hệ thống **Quản lý đầu sách (`Book Management`)**.

Hệ thống được thiết kế cho nhóm 8 thành viên với các tính năng mở rộng thực tế:
- **Xác thực và phân quyền 2 cấp độ:** Quản trị viên (`admin`) và Người dùng (`user`) thông qua `AuthController` và `AdminMiddleware`.
- **Chuẩn hóa dữ liệu 3NF:** Tách bảng Thể loại (`categories`), Tác giả (`authors`), Nhà xuất bản (`publishers`) và liên kết nhiều - nhiều (`N - N`) với bảng Sách (`books`) thông qua 2 bảng trung gian `author_book` và `book_category`.
- **Tìm kiếm, lọc đa tiêu chí và sắp xếp:** Tra cứu theo từ khóa (tên sách, mã ISBN, tên tác giả), lọc theo thể loại, tình trạng kho, khoảng giá và sắp xếp theo giá bán thông qua Eloquent Local Scope (`Book::scopeFilter`).
- **Kiểm soát tồn kho trực quan:** Phân loại tự động 3 trạng thái *Còn hàng*, *Sắp hết hàng* (`1..4` quyển, dưới ngưỡng `Book::LOW_STOCK = 5`) và *Hết hàng* (`0` quyển).
- **Quản lý Thùng rác (Soft Delete):** Hỗ trợ xóa mềm đưa sách vào thùng rác, khôi phục lại sách hoặc xóa vĩnh viễn (`forceDelete`) kèm dọn dẹp file ảnh bìa.

### 1.2. Kết quả đầu ra yêu cầu
- Khởi chạy thành công ứng dụng trên môi trường cục bộ (`localhost`).
- Nhận diện và giải thích rõ vai trò của Route, Middleware, Controller, Form Request, Model, View và Migration.
- Thực hiện đầy đủ các thao tác: đăng nhập/đăng xuất, xem danh sách, xem nhanh (Quick View Modal), xem chi tiết kèm sách cùng thể loại, thêm mới (hỗ trợ thêm nhanh tác giả mới ngay trên form), chỉnh sửa, xóa mềm, khôi phục và xóa vĩnh viễn.
- Toàn bộ mã nguồn được quản lý phiên bản trên GitHub, có bộ kiểm thử tự động (Feature Tests) và dữ liệu mẫu thực tế (**58 đầu sách**, **68 tác giả**, **6 thể loại**, **20 nhà xuất bản** kèm **58 ảnh bìa nội bộ**).

### 1.3. Luồng xử lý chuẩn theo mô hình MVC

```text
  [ Trình duyệt (Browser / Client) ]
                 │
                 │ 1. Gửi HTTP Request (VD: GET /books hoặc POST /books)
                 ▼
     [ Route (routes/web.php) ]
                 │
                 │ 2. Kiểm tra qua Middleware (auth, admin, throttle)
                 ▼
  [ Form Request (app/Http/Requests) ]  ◄── (Chuẩn hóa & Validate dữ liệu khi Store/Update)
                 │
                 │ 3. Chuyển dữ liệu hợp lệ vào Action tương ứng
                 ▼
  [ Controller (app/Http/Controllers) ]
                 │
                 │ 4. Gọi Model Eloquent (trong DB::transaction) & lưu file Storage
                 ▼
    [ Model Eloquent (app/Models) ]
                 │
                 │ 5. Thực thi truy vấn SQL (ORM) & trả kết quả
                 ▼
     [ Cơ sở dữ liệu (MySQL) ]
                 │
                 │ 6. Trả dữ liệu bản ghi về Controller -> truyền sang View
                 ▼
    [ View Blade (resources/views) ]
                 │
                 │ 7. Render mã HTML hoàn chỉnh (Bootstrap 5 + CSS/JS)
                 ▼
  [ Trình duyệt (Browser / Client) ]
```

#### Minh họa thực tế trong dự án: Luồng chức năng "Thêm sách mới"

Để hình dung rõ cách các thành phần trên phối hợp với nhau, hãy theo dõi trọn vẹn vòng đời của thao tác **Thêm sách mới** trong mã nguồn:

1. **Bước 1 — Hiển thị biểu mẫu nhập liệu (`GET /books/create`):**
   - Quản trị viên bấm nút **"Thêm sách"** trên trang danh sách (`resources/views/books/index.blade.php`).
   - Route `books.create` trong `routes/web.php` kiểm tra qua 2 lớp middleware `auth` và `admin` (`app/Http/Middleware/AdminMiddleware.php`), sau đó gọi `BookController@create`.
   - `BookController@create` gọi hàm `formOptions()` truy vấn danh sách `Author`, `Category`, `Publisher` theo thứ tự bảng chữ cái rồi trả về view `resources/views/books/create.blade.php` (view này nhúng biểu mẫu dùng chung `resources/views/books/partials/form.blade.php` với `['mode' => 'create']`).

2. **Bước 2 — Gửi dữ liệu từ Form (`POST /books`):**
   - Khi bấm **"Lưu sách"**, thẻ `<form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">` trong `resources/views/books/partials/form.blade.php` gửi dữ liệu lên server, bao gồm:
     - Các trường đơn của bảng `books`: `title`, `isbn`, `publisher_id`, `published_year`, `price`, `stock_quantity`, `page_count`, `dimensions`, `description`.
     - Các mảng liên kết nhiều - nhiều: `author_ids[]` (tác giả có sẵn), `new_authors[]` (tên tác giả mới gõ trực tiếp trên dropdown), `category_ids[]` (thể loại).
     - Tập tin ảnh bìa đính kèm: `cover_image`.

3. **Bước 3 — Định tuyến và Kiểm tra phân quyền (`Route` $\rightarrow$ `Middleware`):**
   - `routes/web.php` khớp yêu cầu `POST /books` với route `books.store` (`BookController@store`).
   - Middleware `auth` và `admin` kiểm tra phiên đăng nhập và gọi `$request->user()->isAdmin()`. Nếu là tài khoản `user` thường, hệ thống chặn ngay với mã lỗi `403 Forbidden`.

4. **Bước 4 — Chuẩn hóa và Xác thực dữ liệu (`Form Request`):**
   - Trước khi vào hàm `store()`, Laravel tự động khởi tạo `app/Http/Requests/StoreBookRequest.php`:
     - **`prepareForValidation()`:** Tự động xóa dấu gạch ngang/khoảng trắng trong `isbn` (`978-604-...` $\rightarrow$ `978604...`), lọc bỏ các lựa chọn rỗng trong `author_ids`, `category_ids` và chuẩn hóa mảng `new_authors`.
     - **`rules()` & `messages()`:** Kiểm tra dữ liệu (tên sách bắt buộc, ISBN không trùng, giá và tồn kho không âm, có ít nhất 1 tác giả và 1 thể loại, ảnh bìa đúng định dạng `jpg, jpeg, png, webp` tối đa `2MB`...).
     - **Nếu dữ liệu sai:** Tự động chuyển hướng (`Redirect 302`) quay lại trang `/books/create` kèm các thông báo lỗi tiếng Việt (`$errors`) và giữ nguyên toàn bộ nội dung vừa nhập (`old()`).

5. **Bước 5 — Xử lý nghiệp vụ, Lưu ảnh và Giao dịch CSDL (`Controller` $\rightarrow$ `Model` $\rightarrow$ `Database`):**
   - Khi dữ liệu hợp lệ, `BookController@store` gọi phương thức `persist(new Book, $request)`:
     - **Lưu file ảnh bìa:** Gọi `$request->storeCover()` lưu file tải lên vào `storage/app/public/covers/{ten_file_ngau_nhien}.jpg` và trả về đường dẫn `'covers/...'`.
     - **Mở giao dịch (`DB::transaction`)** để đảm bảo tính toàn vẹn (hoặc lưu thành công tất cả, hoặc không lưu gì cả):
       1. **Lưu bảng `books`:** Gọi `$book->fill($request->bookData())` (hàm `bookData()` đã lọc bỏ `author_ids`, `new_authors`, `category_ids`, `cover_image`), gán `$book->cover_image_url = $newCover` rồi gọi `$book->save()` để thực thi `INSERT INTO books`.
       2. **Tạo tác giả mới (nếu có):** Duyệt mảng `new_authors`, gọi `Author::firstOrCreate(['name' => $authorName])` để lấy `id` và gộp vào danh sách `$authorIds`.
       3. **Lưu quan hệ nhiều - nhiều (Bảng trung gian):**
          - Gọi `$book->authors()->sync($authorIds)` để ghi các cặp `(author_id, book_id)` vào bảng `author_book`.
          - Gọi `$book->categories()->sync($request->validated('category_ids'))` để ghi các cặp `(book_id, category_id)` vào bảng `book_category`.
     - **Xử lý an toàn khi lỗi CSDL:** Nếu trong khối `DB::transaction` xảy ra lỗi (`Throwable`), khối `catch` sẽ xóa ngay file ảnh vừa tải lên (`Storage::disk('public')->delete($newCover)`) để không để lại file rác, sau đó hàm `saveFailed()` ghi log và báo lỗi về lại form.

6. **Bước 6 — Phản hồi và Hiển thị giao diện kết quả (`Controller` $\rightarrow$ `View`):**
   - Sau khi lưu thành công, `BookController@store` trả về:
     ```php
     return to_route('books.edit', $book)->with('success', 'Đã thêm sách mới thành công.');
     ```
   - Trình duyệt chuyển sang đường dẫn `GET /books/{id}/edit` (`BookController@edit`), nạp cuốn sách vừa tạo cùng các quan hệ `authors`, `categories`, `publisher` và kết xuất giao diện `resources/views/books/edit.blade.php` (kế thừa khung `resources/views/layouts/app.blade.php` hiển thị hộp thông báo xanh *"Đã thêm sách mới thành công."*).

---

## 2. Hướng dẫn thiết lập và khởi chạy dự án cho thành viên nhóm

Mục này dành cho các thành viên trong nhóm sau khi clone mã nguồn từ GitHub về máy cá nhân để thiết lập môi trường chạy được ngay trong vài phút.

### 2.1. Quy trình thiết lập nhanh

Mở Terminal (**Git Bash**, **PowerShell** hoặc **CMD**) tại thư mục dự án vừa clone và thực hiện tuần tự các bước sau:

#### Bước 1. Cài đặt các thư viện phụ thuộc (Vendor)
```bash
composer install
```
*(Lưu ý: Luôn dùng `composer install` để cài đặt theo đúng phiên bản trong `composer.lock`, không dùng `composer update`).*

#### Bước 2. Tạo file cấu hình môi trường `.env`
```bash
copy .env.example .env
```

#### Bước 3. Sinh khóa ứng dụng (App Key)
```bash
php artisan key:generate
```

#### Bước 4. Tạo cơ sở dữ liệu trong MySQL
1. Khởi động **MySQL** từ bảng điều khiển XAMPP Control Panel.
2. Truy cập phpMyAdmin tại: `http://localhost/phpmyadmin/`.
3. Tạo mới database có tên: `book_store_db` (chọn bảng mã `utf8mb4_unicode_ci`).
4. Mở file `.env`, cập nhật phần kết nối cơ sở dữ liệu sang MySQL (vì `.env.example` mặc định để `sqlite`):
   ```dotenv
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=book_store_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

#### Bước 5. Tạo liên kết Symbolic Link cho thư mục ảnh bìa
```bash
php artisan storage:link
```
*(Lệnh này tạo liên kết từ `public/storage` sang `storage/app/public` để trình duyệt web hiển thị được 58 ảnh bìa sách trong `storage/app/public/covers`).*

#### Bước 6. Tạo bảng và nạp dữ liệu mẫu
```bash
php artisan migrate --seed
```
Lệnh này sẽ tự động khởi tạo toàn bộ 12 bảng CSDL, nạp sẵn **6 thể loại**, **68 tác giả**, **20 nhà xuất bản**, **58 đầu sách thực tế** (kèm sao chép 58 ảnh bìa từ `database/data/covers/` sang `storage/app/public/covers/`) và **2 tài khoản kiểm thử**.

> **Mẹo thiết lập nhanh 1 lệnh:** Nếu dùng SQLite hoặc đã cấu hình xong `.env`, bạn có thể chạy `composer run setup`. *(Lưu ý: Lệnh này thực thi `migrate:fresh --seed` nên sẽ xóa toàn bộ dữ liệu cũ trong DB trước khi tạo mới).*

#### Bước 7. Khởi chạy máy chủ nội bộ
```bash
php artisan serve
```
Mở trình duyệt truy cập: `http://127.0.0.1:8000` hoặc `http://localhost:8000`.

### 2.2. Tài khoản kiểm thử hệ thống

Sau khi chạy lệnh seed (`DatabaseSeeder.php`), hệ thống cung cấp sẵn 2 tài khoản phân quyền để kiểm thử:

| Vai trò | Địa chỉ Email | Mật khẩu | Quyền hạn thực tế |
|---|---|---|---|
| **Quản trị viên (`admin`)** | `admin@example.com` | `12345678` | Toàn quyền xem, lọc, thêm sách mới, thêm tác giả nhanh, chỉnh sửa, xóa mềm, xem thùng rác, khôi phục và xóa vĩnh viễn. |
| **Nhân viên / Bạn đọc (`user`)** | `user@example.com` | `12345678` | Xem danh sách, tìm kiếm, lọc theo tiêu chí, xem nhanh (Quick View) và xem chi tiết sách. |

### 2.3. Chạy bộ kiểm thử tự động (PHPUnit)

Dự án đi kèm bộ kiểm thử tự động trong `tests/Feature/` chạy trên cơ sở dữ liệu bộ nhớ tạm (`sqlite :memory:`), hoàn toàn không làm ảnh hưởng đến dữ liệu trong `book_store_db`:

```bash
php artisan test --compact
# Hoặc trên Windows PowerShell:
.\vendor\bin\phpunit.bat
```

---

## 3. Chuẩn bị môi trường và kiểm tra cài đặt

### 3.1. Bảng yêu cầu môi trường

| Thành phần | Phiên bản yêu cầu | Mục đích sử dụng | Ghi chú |
|---|---|---|---|
| **PHP** | 8.4.x (tối thiểu `>= 8.3`) | Thông dịch và thực thi mã nguồn Laravel 13 | Bật đủ các extension cần thiết |
| **Composer** | 2.x | Quản lý các gói thư viện mã nguồn PHP | Chạy đồng bộ cùng phiên bản PHP |
| **Hệ quản trị CSDL** | MySQL / MariaDB (XAMPP) | Lưu trữ cơ sở dữ liệu `book_store_db` | Khởi động dịch vụ qua XAMPP Control Panel |
| **Git** | Phiên bản mới nhất | Quản lý mã nguồn, tạo nhánh và commit | Kết nối với GitHub repository |
| **IDE** | Visual Studio Code | Trình soạn thảo viết mã nguồn | Khuyến nghị cài Extension Laravel |

### 3.2. Kiểm tra phiên bản công cụ

Mở cửa sổ dòng lệnh và thực hiện kiểm tra:

```bash
php -v
composer --version
git --version
```
**Kết quả mong đợi:** Các lệnh hiển thị thông tin phiên bản rõ ràng, không xuất hiện thông báo *"not recognized"*.

### 3.3. Cấu hình extension PHP 8.4

Mở file cấu hình `php.ini` đang sử dụng và kiểm tra đảm bảo các extension sau đã được kích hoạt (bỏ dấu `;` ở đầu dòng):

```ini
extension=curl
extension=fileinfo
extension=gd
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=pdo_sqlite
extension=zip
```

---

## 4. Nhận diện cấu trúc thư mục của dự án

### 4.1. Bảng vai trò các thư mục và tập tin cốt lõi

Bảng phân tích vai trò các thư mục và tập tin cốt lõi trong dự án theo chuẩn kiến trúc Laravel 13:

| Thư mục / Tập tin | Vai trò trong hệ thống | Thành phần MVC |
|---|---|:---:|
| `app/Models/` | Chứa các lớp thực thể Eloquent ORM (`Book`, `Category`, `Author`, `Publisher`, `User`), định nghĩa quan hệ, `scopeFilter` và xử lý đường dẫn ảnh bìa (`coverUrl`) | **Model** |
| `app/Http/Controllers/` | Chứa các lớp điều khiển: `AuthController` (đăng nhập/đăng xuất), `BookController` (CRUD sách), `BookTrashController` (thùng rác, khôi phục, xóa vĩnh viễn) | **Controller** |
| `app/Http/Requests/` | Chứa các lớp Form Request (`StoreBookRequest`, `UpdateBookRequest`) phục vụ chuẩn hóa dữ liệu, xác thực (Validation) và lưu file ảnh bìa | **Request** |
| `app/Http/Middleware/` | Chứa `AdminMiddleware` kiểm tra quyền `role === 'admin'` trước khi cho phép truy cập các thao tác quản trị | **Middleware** |
| `app/Policies/` & `app/Providers/` | Chứa `BookPolicy`, `AppServiceProvider` (cấu hình phân trang Bootstrap 5) và `AuthServiceProvider` (đăng ký Gate `'admin'`) | **Policy / Provider** |
| `resources/views/` | Chứa các tệp giao diện Blade Template (`layouts/app`, `auth/login`, `books/index`, `show`, `create`, `edit`, `trash` và `books/partials/*`) | **View** |
| `routes/web.php` | Định nghĩa toàn bộ 14 điểm cuối URL của ứng dụng, gắn middleware `auth`, `admin`, `throttle` và ánh xạ tới Controller | **Route** |
| `database/migrations/` | Chứa 11 file định nghĩa cấu trúc bảng CSDL (gồm `users`, `categories`, `authors`, `publishers`, `books`, `author_book`, `book_category`...) | **Migration** |
| `database/seeders/` | Chứa 5 file Seeder (`DatabaseSeeder`, `CategorySeeder`, `AuthorSeeder`, `PublisherSeeder`, `BookSeeder`) nạp dữ liệu mẫu | **Seeder** |
| `database/data/covers/` | Lưu trữ 58 ảnh bìa sách gốc (`1.jpg` $\rightarrow$ `58.jpg`) được Git theo dõi để `BookSeeder` sao chép sang `storage` khi khởi tạo | **Data Asset** |
| `storage/app/public/covers/` | Thư mục lưu trữ các tập tin ảnh bìa sách đang phục vụ trên web (`Book::COVER_DIR = 'covers'`) | **Storage** |
| `public/` | Thư mục gốc của web server, chứa `index.php`, CSS (`admin-figma.css`, `book-form.css`), JS (`book-form.js`), fonts (`Inter`) và symlink `storage` | **Public** |
| `tests/Feature/` | Chứa các file kiểm thử tự động kiểm tra phân quyền, lọc sách, validate form, CRUD và thùng rác | **Testing** |
| `.env` | Tập tin cấu hình môi trường cục bộ (không đưa lên GitHub) | **Config** |

### 4.2. Lộ trình đọc code nhanh cho thành viên mới

Khi mới mở mã nguồn dự án, thay vì mở từng file ngẫu nhiên, thành viên nên đọc lần lượt theo **4 chặng từ dễ đến khó** dưới đây để nắm trọn vẹn hệ thống trong ~15 phút:

1. **Chặng 1 — Nắm bản đồ đường đi và phân quyền (`Route` & `Middleware`):**
   - Đọc `routes/web.php` đầu tiên để biết toàn bộ 14 URL của ứng dụng, URL nào gọi Controller nào và URL nào yêu cầu quyền `admin`.
   - Đọc `bootstrap/app.php` và `app/Http/Middleware/AdminMiddleware.php` để hiểu cách hệ thống chặn tài khoản thường (`403 Forbidden`).
2. **Chặng 2 — Nắm cấu trúc bảng và Model trung tâm (`Migration` & `Model`):**
   - Xem nhanh các file trong `database/migrations/2026_10_*` để nhớ tên cột chính xác của các bảng.
   - Đọc `app/Models/Book.php` — đây là file Model quan trọng nhất chứa hằng số ngưỡng tồn kho (`LOW_STOCK = 5`), thư mục ảnh (`COVER_DIR = 'covers'`), các mối quan hệ (`publisher`, `authors`, `categories`), hàm lọc `scopeFilter()` và thuộc tính lấy ảnh bìa `$book->cover_url`.
3. **Chặng 3 — Nắm luồng xử lý nghiệp vụ và kiểm tra dữ liệu (`Form Request` & `Controller`):**
   - Đọc `app/Http/Requests/StoreBookRequest.php` và `UpdateBookRequest.php` để biết dữ liệu đầu vào được làm sạch (`prepareForValidation`) và kiểm tra (`rules`) như thế nào.
   - Đọc `app/Http/Controllers/BookController.php` (đặc biệt là hàm `persist()` dùng chung cho cả Thêm và Sửa), sau đó đọc `BookTrashController.php` và `AuthController.php`.
4. **Chặng 4 — Nắm giao diện và tương tác phía trình duyệt (`Blade View`, `CSS` & `JS`):**
   - Đọc khung giao diện chung `resources/views/layouts/app.blade.php` và file định kiểu `public/css/admin-figma.css`.
   - Đọc `resources/views/books/index.blade.php` cùng các mảnh ghép trong `resources/views/books/partials/` (`filter-bar.blade.php`, `form.blade.php`, `quick-view-modal.blade.php`, `stock-badge.blade.php`) và file xử lý tương tác form `public/js/book-form.js`.

---

## 5. Thiết kế cơ sở dữ liệu và cấu hình kết nối

### 5.1. Sơ đồ quan hệ thực thể (ERD Chuẩn 3NF)

Cơ sở dữ liệu sử dụng mối quan hệ **Nhiều - Nhiều (`N - N`)** giữa Sách với Tác giả (`author_book`) và Thể loại (`book_category`), cùng quan hệ **Một - Nhiều (`1 - N`)** giữa Nhà xuất bản và Sách:

```text
    ┌──────────────┐1             N┌─────────────────┐N             1┌────────────────────┐
    │  categories  │───────────────│  book_category  │───────────────│       books        │
    │──────────────│               │─────────────────│               │────────────────────│
    │ id (PK)      │               │ book_id (PK,FK) │               │ id (PK)            │
    │ name (UQ)    │               │ category_id     │               │ isbn (UQ, Nullable)│
    └──────────────┘               │   (PK,FK)       │               │ title              │
                                   └─────────────────┘               │ published_year     │
    ┌──────────────┐1             N┌─────────────────┐N             1│ price              │
    │   authors    │───────────────│   author_book   │───────────────│ stock_quantity     │
    │──────────────│               │─────────────────│               │ page_count         │
    │ id (PK)      │               │ author_id(PK,FK)│               │ dimensions         │
    │ name         │               │ book_id (PK,FK) │               │ cover_image_url    │
    │ biography    │               └─────────────────┘               │ description        │
    └──────────────┘                                                 │ publisher_id (FK)  │
                                                                     │ deleted_at (Soft)  │
    ┌──────────────┐1                                               N└────────────────────┘
    │  publishers  │───────────────────────────────────────────────────────────┘
    │──────────────│
    │ id (PK)      │
    │ name (UQ)    │
    │ address      │
    └──────────────┘
```

### 5.2. Đặc tả chi tiết các bảng dữ liệu nghiệp vụ

#### Bảng `categories` (Thể loại sách — Không dùng `timestamps`)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Khóa chính thể loại |
| `name` | `VARCHAR(150)` | Unique, Not Null | Tên thể loại sách |

#### Bảng `authors` (Tác giả — Không dùng `timestamps`)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Khóa chính tác giả |
| `name` | `VARCHAR(150)` | Not Null | Họ tên tác giả |
| `biography` | `TEXT` | Nullable | Tiểu sử tác giả |

#### Bảng `publishers` (Nhà xuất bản — Không dùng `timestamps`)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Khóa chính nhà xuất bản |
| `name` | `VARCHAR(150)` | Unique, Not Null | Tên nhà xuất bản |
| `address` | `VARCHAR(255)` | Nullable | Địa chỉ trụ sở NXB |

#### Bảng `books` (Bảng trọng tâm — Không dùng `timestamps`, có `SoftDeletes`)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Khóa chính đầu sách |
| `isbn` | `VARCHAR(20)` | Unique, Nullable | Mã sách / Mã tiêu chuẩn quốc tế ISBN |
| `title` | `VARCHAR(255)` | Not Null | Tên đầu sách |
| `published_year` | `YEAR` | Nullable | Năm xuất bản (`1901` $\rightarrow$ năm hiện tại) |
| `price` | `DECIMAL(12,2)` | Default: `0`, Not Null | Giá bán thực tế (VNĐ) |
| `stock_quantity` | `INT UNSIGNED` | Default: `0`, Not Null | Số lượng tồn kho thực tế |
| `page_count` | `INT UNSIGNED` | Nullable | Số trang sách |
| `dimensions` | `VARCHAR(50)` | Nullable | Kích thước (VD: `20 x 14.5 x 1.3 cm`) |
| `cover_image_url` | `VARCHAR(255)` | Nullable | Đường dẫn ảnh bìa trên disk `public` (`covers/...`) hoặc URL ngoài |
| `description` | `TEXT` | Nullable | Mô tả nội dung chi tiết |
| `publisher_id` | `BIGINT UNSIGNED` | Nullable, FK $\rightarrow$ `publishers(id)` (`nullOnDelete`) | Tham chiếu nhà xuất bản |
| `deleted_at` | `TIMESTAMP` | Nullable | Dấu thời gian xóa mềm (khác `NULL` là đang ở Thùng rác) |

#### Bảng trung gian `author_book` và `book_category`
| Tên bảng | Các cột khóa chính & khóa ngoại | Cơ chế toàn vẹn |
|---|---|---|
| `author_book` | `author_id` (`FK -> authors.id`), `book_id` (`FK -> books.id`) | Khóa chính phức hợp `(author_id, book_id)`, tự động xóa liên kết khi xóa bản ghi gốc (`cascadeOnDelete`) |
| `book_category` | `book_id` (`FK -> books.id`), `category_id` (`FK -> categories.id`) | Khóa chính phức hợp `(book_id, category_id)`, tự động xóa liên kết khi xóa bản ghi gốc (`cascadeOnDelete`) |

#### Bảng `users` (Tài khoản & Phân quyền)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key, Auto Increment | Khóa chính người dùng |
| `name` | `VARCHAR(255)` | Not Null | Tên hiển thị người dùng |
| `email` | `VARCHAR(255)` | Unique, Not Null | Địa chỉ email đăng nhập |
| `email_verified_at` | `TIMESTAMP` | Nullable | Thời điểm xác thực email |
| `password` | `VARCHAR(255)` | Not Null | Mật khẩu đã mã hóa (`hashed` / Bcrypt) |
| `role` | `VARCHAR(30)` | Default: `'user'`, Not Null | Vai trò phân quyền: `'admin'` hoặc `'user'` |
| `remember_token` | `VARCHAR(100)` | Nullable | Token duy trì đăng nhập |
| `created_at`, `updated_at` | `TIMESTAMP` | Nullable | Thời điểm khởi tạo và cập nhật tài khoản |

### 5.3. Tạo cơ sở dữ liệu và cấu hình file `.env`

Tạo database trong phpMyAdmin hoặc qua MySQL Client:
```sql
CREATE DATABASE book_store_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Cấu hình các tham số tương ứng trong `.env`:
```dotenv
APP_NAME="Book Management"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_store_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## 6. Xây dựng chức năng CRUD theo mô hình MVC

### 6.1. Định tuyến (`routes/web.php`)
Hệ thống định nghĩa 14 route được bảo vệ chặt chẽ bởi middleware `auth`, `admin` (`AdminMiddleware`) và `throttle:5,1`:

```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BookTrashController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('books.index');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    // Chỉ admin được xem thùng rác, khôi phục hoặc xóa sách vĩnh viễn
    Route::prefix('books/trash')->middleware('admin')->group(function () {
        Route::get('/', [BookTrashController::class, 'index'])->name('books.trash');
        Route::post('/{id}/restore', [BookTrashController::class, 'restore'])->name('books.restore');
        Route::delete('/{id}/force-delete', [BookTrashController::class, 'forceDelete'])->name('books.forceDelete');
    });

    // Cả user và admin được xem danh sách/chi tiết; chỉ admin được thêm/sửa/xóa
    Route::resource('books', BookController::class)
        ->middlewareFor(['index', 'show'], ['auth'])
        ->middlewareFor(['create', 'store', 'edit', 'update', 'destroy'], ['auth', 'admin']);
});
```

### 6.2. Bộ điều khiển (`Controllers`) và `Form Requests`

1. **`BookController` (`app/Http/Controllers/BookController.php`):**
   - `index(Request $request)`: Nạp sẵn quan hệ (`with(['authors', 'categories', 'publisher'])`), áp dụng bộ lọc `->filter($request->only(Book::FILTER_KEYS))`, sắp xếp mới nhất và phân trang 10 bản ghi/trang kèm `withQueryString()`.
   - `create()` & `edit(Book $book)`: Cung cấp danh sách tác giả, thể loại, nhà xuất bản cho form.
   - `store(StoreBookRequest $request)` & `update(UpdateBookRequest $request, Book $book)`: Cùng gọi phương thức `persist()` bên trong `DB::transaction()` để lưu thông tin sách, tự động tạo tác giả mới (`Author::firstOrCreate`) nếu người dùng nhập `new_authors[]`, đồng bộ bảng trung gian (`sync`) và quản lý file ảnh bìa an toàn (xóa ảnh mới tải lên nếu transaction lỗi; xóa ảnh cũ sau khi cập nhật ảnh mới thành công).
   - `show(Book $book)`: Hiển thị chi tiết sách kèm tối đa 4 cuốn sách liên quan cùng thể loại (`$related`).
   - `destroy(Book $book)`: Thực hiện xóa mềm (`$book->delete()`), giữ nguyên liên kết và ảnh bìa để có thể khôi phục từ thùng rác.
2. **`BookTrashController` (`app/Http/Controllers/BookTrashController.php`):**
   - `index()`: Hiển thị danh sách sách trong thùng rác (`Book::onlyTrashed()`).
   - `restore(int $id)`: Khôi phục sách đã xóa mềm (`$book->restore()`).
   - `forceDelete(int $id)`: Xóa vĩnh viễn bản ghi trong `DB::transaction` (`detach()` tác giả và thể loại, `forceDelete()`) và xóa file ảnh bìa vật lý (`$book->deleteCoverFile()`).
3. **`StoreBookRequest` & `UpdateBookRequest` (`app/Http/Requests/`):**
   - Chuẩn hóa mã ISBN (bỏ dấu gạch ngang và khoảng trắng), lọc bỏ các lựa chọn rỗng trong `prepareForValidation()`.
   - `UpdateBookRequest` kế thừa `StoreBookRequest`, khóa không cho phép thay đổi mã ISBN đối với sách đã có ISBN, đồng thời cho phép để trống NXB/năm xuất bản với các sách cũ.

### 6.3. Mô hình dữ liệu (`Models & Eloquent Relationships`)
Các quan hệ dữ liệu được thiết lập trong `app/Models/`:
- **`Category`**: `belongsToMany(Book::class, 'book_category')`
- **`Author`**: `belongsToMany(Book::class, 'author_book')`
- **`Publisher`**: `hasMany(Book::class)`
- **`Book`**:
  - `belongsTo(Publisher::class)`
  - `belongsToMany(Author::class, 'author_book')`
  - `belongsToMany(Category::class, 'book_category')`
  - Kích hoạt Trait: `use SoftDeletes;`
  - Định nghĩa `scopeFilter(Builder $query, array $filters)` xử lý tìm kiếm và lọc theo `Book::FILTER_KEYS` (`['keyword', 'category_id', 'stock', 'price_min', 'price_max', 'sort']`).
- **`User`**: Cung cấp phương thức kiểm tra vai trò quản trị:
  ```php
  public function isAdmin(): bool
  {
      return $this->role === 'admin';
  }
  ```

### 6.4. Giao diện người dùng (`Blade Views & Bootstrap 5`)
- **Khung giao diện chung:** `resources/views/layouts/app.blade.php` (tích hợp Bootstrap `5.3.3`, Bootstrap Icons `1.11.3`, `public/css/admin-figma.css`, menu tài khoản và thông báo Flash).
- **Trang xác thực:** `resources/views/auth/login.blade.php`.
- **Các trang quản lý sách (`resources/views/books/`):** `index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`, `trash.blade.php`.
- **Các mảnh giao diện tái sử dụng (`resources/views/books/partials/`):**
  - `filter-bar.blade.php`: Thanh tìm kiếm từ khóa, lọc thể loại, tình trạng kho, khoảng giá và sắp xếp.
  - `form.blade.php`: Form nhập liệu dùng chung cho cả Thêm mới (`mode => 'create'`) và Chỉnh sửa (`mode => 'edit'`), kết hợp với `public/css/book-form.css` và `public/js/book-form.js`.
  - `quick-view-modal.blade.php`: Hộp thoại xem nhanh thông tin sách ngay trên trang danh sách.
  - `stock-badge.blade.php`: Nhãn hiển thị trạng thái tồn kho trực quan.

### 6.5. Quy ước xử lý ảnh bìa và trạng thái tồn kho
1. **Hiển thị ảnh bìa:** Cột trong CSDL là `cover_image_url` (lưu đường dẫn tương đối `covers/{file}.jpg` trên disk `public`). Khi hiển thị trên Blade View, sử dụng thuộc tính tính toán (Accessor) `$book->cover_url` được định nghĩa trong `app/Models/Book.php`:
   ```blade
   @if($book->cover_url)
       <img src="{{ $book->cover_url }}" alt="Bìa sách {{ $book->title }}">
   @endif
   ```
2. **Số lượng và ngưỡng cảnh báo tồn kho:** Cột trong CSDL là `stock_quantity`. Ngưỡng cảnh báo sắp hết hàng được định nghĩa tại hằng số `Book::LOW_STOCK = 5`:
   - `stock_quantity === 0`: **Hết hàng** (`out`)
   - `1 <= stock_quantity < 5`: **Sắp hết** (`low`)
   - `stock_quantity >= 5`: **Còn hàng** (`available`)

### 6.6. Bảng tra cứu vị trí từng chức năng trong mã nguồn

Bảng dưới đây ánh xạ từng chức năng trên giao diện với đầy đủ các file xử lý từ Route, Controller, Request/Model đến View và Test để thành viên tra cứu nhanh:

| Chức năng | URL & Route Name | Controller & Method | Request / Model xử lý | File Giao diện (Blade / CSS / JS) | File Kiểm thử (Feature Test) |
|---|---|---|---|---|---|
| **Đăng nhập / Đăng xuất** | `GET /login` (`login`)<br>`POST /login`<br>`POST /logout` (`logout`) | `AuthController@showLoginForm`<br>`AuthController@login`<br>`AuthController@logout` | `User` (`isAdmin()`) | `resources/views/auth/login.blade.php`<br>`resources/views/layouts/app.blade.php` | `tests/Feature/BookPermissionTest.php` |
| **Xem danh sách & Phân trang** | `GET /books`<br>(`books.index`) | `BookController@index` | `Book` (`with`, `latest`, `paginate(10)`) | `resources/views/books/index.blade.php`<br>`resources/views/books/partials/stock-badge.blade.php` | `tests/Feature/BookControllerTest.php` |
| **Tìm kiếm, Lọc & Sắp xếp sách** | `GET /books?keyword=...`<br>(`books.index`) | `BookController@index` | `Book::FILTER_KEYS`<br>`Book::scopeFilter()` | `resources/views/books/partials/filter-bar.blade.php` | `tests/Feature/BookFilterExportTest.php` |
| **Xem nhanh sách (Quick View)** | *(Mở hộp thoại ngay tại `/books`)* | *(Xử lý tại trình duyệt)* | Thuộc tính `data-*` trên nút *"Xem"* | `resources/views/books/partials/quick-view-modal.blade.php` | — |
| **Xem chi tiết & Sách liên quan** | `GET /books/{book}`<br>(`books.show`) | `BookController@show` | `Book` (`load`, `whereHas('categories')`) | `resources/views/books/show.blade.php` | `tests/Feature/BookFilterExportTest.php` |
| **Thêm sách mới (+ tạo nhanh tác giả)** | `GET /books/create` (`books.create`)<br>`POST /books` (`books.store`) | `BookController@create`<br>`BookController@store` $\rightarrow$ `persist()` | `StoreBookRequest`<br>`Author::firstOrCreate()` | `resources/views/books/create.blade.php`<br>`resources/views/books/partials/form.blade.php`<br>`public/css/book-form.css`, `public/js/book-form.js` | `tests/Feature/BookFormTest.php`<br>`tests/Feature/BookRequestValidationTest.php` |
| **Chỉnh sửa sách** | `GET /books/{book}/edit` (`books.edit`)<br>`PUT /books/{book}` (`books.update`) | `BookController@edit`<br>`BookController@update` $\rightarrow$ `persist()` | `UpdateBookRequest`<br>(`isbnRules()`) | `resources/views/books/edit.blade.php`<br>`resources/views/books/partials/form.blade.php`<br>`public/css/book-form.css`, `public/js/book-form.js` | `tests/Feature/BookFormTest.php`<br>`tests/Feature/BookRequestValidationTest.php` |
| **Xóa mềm (Đưa vào thùng rác)** | `DELETE /books/{book}`<br>(`books.destroy`) | `BookController@destroy` | `Book::delete()` (`SoftDeletes`) | Modal `#deleteBook{id}` trong `resources/views/books/index.blade.php` | `tests/Feature/BookTrashTest.php` |
| **Thùng rác, Khôi phục & Xóa vĩnh viễn** | `GET /books/trash` (`books.trash`)<br>`POST /books/trash/{id}/restore` (`books.restore`)<br>`DELETE /books/trash/{id}/force-delete` (`books.forceDelete`) | `BookTrashController@index`<br>`BookTrashController@restore`<br>`BookTrashController@forceDelete` | `Book::onlyTrashed()`<br>`Book::deleteCoverFile()` | `resources/views/books/trash.blade.php` | `tests/Feature/BookTrashTest.php` |

### 6.7. Chỉ dẫn nhanh: Muốn sửa gì thì mở file nào?

Khi nhận một nhiệm vụ chỉnh sửa hoặc bổ sung tính năng, hãy tra cứu nhanh vị trí cần sửa dưới đây:

- **Muốn sửa bố cục chung (Thanh menu, thông báo thành công/lỗi, chân trang, màu sắc chủ đạo):**
  - Mở `resources/views/layouts/app.blade.php` và `public/css/admin-figma.css`. *(Lưu ý: Dự án nạp trực tiếp CSS từ `public/css/`, không sử dụng `resources/css/app.css`)*.
- **Muốn sửa giao diện hoặc hành vi trên form Thêm mới / Chỉnh sửa sách:**
  - Mở `resources/views/books/partials/form.blade.php`, `public/css/book-form.css` và `public/js/book-form.js`.
  - Cả hai trang `create.blade.php` và `edit.blade.php` đều dùng chung file `partials/form.blade.php` (phân biệt bằng biến `$editing = $mode === 'edit'`).
- **Muốn thêm hoặc sửa tiêu chí Tìm kiếm / Bộ lọc / Sắp xếp:**
  - Cần cập nhật đồng bộ tại **3 nơi**:
    1. Thêm tên tham số vào mảng hằng số `Book::FILTER_KEYS` trong `app/Models/Book.php`.
    2. Bổ sung điều kiện truy vấn trong hàm `Book::scopeFilter()` (`app/Models/Book.php`).
    3. Thêm ô nhập/chọn tương ứng vào `resources/views/books/partials/filter-bar.blade.php`.
- **Muốn sửa quy tắc kiểm tra dữ liệu (Validate) hoặc câu thông báo lỗi tiếng Việt:**
  - Mở `app/Http/Requests/StoreBookRequest.php` (dùng cho Thêm mới và làm lớp cha) và `app/Http/Requests/UpdateBookRequest.php` (dùng cho Chỉnh sửa).
- **Muốn thay đổi ngưỡng cảnh báo "Sắp hết hàng" (hiện tại là `< 5` quyển):**
  - Chỉ cần sửa giá trị hằng số `public const LOW_STOCK = 5;` trong `app/Models/Book.php`; toàn bộ bộ lọc, huy hiệu tồn kho và cảnh báo trên form sẽ tự động cập nhật theo.

---

## 7. Phân công nhiệm vụ nhóm 8 thành viên

Mô hình phân rã module độc lập giúp 8 thành viên phát triển song song và tích hợp qua Git:

| TT | Thành viên & Module | Nhiệm vụ chính | Các file phụ trách trong mã nguồn | Kiến thức Laravel áp dụng |
|:---:|---|---|---|---|
| **TV1** | **Cơ sở dữ liệu & Model** | - Thiết kế migration cho `books`, `authors`, `publishers`, `categories`, bảng trung gian `author_book`, `book_category`<br>- Bổ sung `stock_quantity`, `deleted_at` vào sách và `role` vào `users`<br>- Thiết lập quan hệ Eloquent và dữ liệu mẫu Seeders | - `database/migrations/...`<br>- `database/seeders/...`<br>- `database/data/covers/`<br>- `app/Models/Book.php`, `Category.php`, `Author.php`, `Publisher.php`, `User.php` | Migration, Foreign Key, Pivot Tables (`BelongsToMany`), SoftDeletes, Database Seeding |
| **TV4: Thương** | **Backend CRUD Sách** | - Xử lý danh sách, chi tiết, thêm, sửa, xóa mềm sách ở tầng backend<br>- Đảm bảo lưu đồng bộ bảng trung gian trong Transaction | - `app/Http/Controllers/BookController.php`<br>- `tests/Feature/BookControllerTest.php` | Controller Resource, Eloquent `sync()`, `DB::transaction` |
| **TV6: Dinh** | **Validation & Upload Ảnh** | - Chuẩn hóa và viết Validation tiếng Việt cho dữ liệu sách<br>- Khóa sửa ISBN cũ khi cập nhật<br>- Xử lý upload và xóa ảnh bìa trong `storage/app/public/covers` | - `app/Http/Requests/StoreBookRequest.php`<br>- `app/Http/Requests/UpdateBookRequest.php`<br>- `tests/Feature/BookRequestValidationTest.php` | Form Request, `prepareForValidation()`, Storage File Upload |
| **TV5: Thắng** | **Giao diện Danh sách & Chi tiết** | - Thiết kế giao diện danh sách, xem nhanh (Quick View Modal), chi tiết sách và sách cùng thể loại<br>- Hiển thị ảnh bìa, giá, tác giả, thể loại, NXB và nhãn tồn kho | - `resources/views/layouts/app.blade.php`<br>- `resources/views/books/index.blade.php`<br>- `resources/views/books/show.blade.php`<br>- `resources/views/books/partials/quick-view-modal.blade.php`<br>- `resources/views/books/partials/stock-badge.blade.php` | Blade Templates, Bootstrap 5 Modal, Component Reusability |
| **TV8: Hưng** | **Giao diện Form Thêm / Sửa** | - Thiết kế giao diện thêm mới và cập nhật sách<br>- Xử lý JS chọn nhiều tác giả/thể loại, thêm nhanh tác giả mới và cập nhật trạng thái tồn kho theo thời gian thực | - `resources/views/books/create.blade.php`<br>- `resources/views/books/edit.blade.php`<br>- `resources/views/books/partials/form.blade.php`<br>- `public/css/book-form.css`, `public/js/book-form.js`<br>- `tests/Feature/BookFormTest.php` | Blade Form, Validation Error Display (`@error`), Vanilla JS DOM |
| **TV2: Cang** | **Lọc, Tìm kiếm & Phân trang** | - Tìm kiếm theo từ khóa (tên sách, ISBN, tác giả)<br>- Lọc theo thể loại, tình trạng kho, khoảng giá và sắp xếp theo giá<br>- Phân trang giữ nguyên tham số truy vấn (`withQueryString`) | - `app/Models/Book.php` (`scopeFilter`)<br>- `resources/views/books/partials/filter-bar.blade.php`<br>- `tests/Feature/BookFilterExportTest.php` | Eloquent Local Scope (`scopeFilter`), `when()`, Pagination `withQueryString()` |
| **TV7: Thúy Duy** | **Đăng nhập & Thùng rác** | - Xử lý đăng nhập, đăng xuất và giới hạn thử sai (`throttle`)<br>- Quản lý thùng rác sách đã xóa mềm, khôi phục và xóa vĩnh viễn | - `app/Http/Controllers/AuthController.php`<br>- `app/Http/Controllers/BookTrashController.php`<br>- `resources/views/auth/login.blade.php`<br>- `resources/views/books/trash.blade.php`<br>- `tests/Feature/BookTrashTest.php` | Authentication, Session Regeneration, SoftDeletes (`onlyTrashed`, `restore`, `forceDelete`) |
| **TV3: Khoa** | **Bảo mật & Phân quyền** | - Thiết lập Middleware chặn User thường truy cập các route quản trị<br>- Ẩn/hiện nút chức năng Thêm/Sửa/Xóa/Thùng rác trên giao diện theo quyền Admin | - `app/Http/Middleware/AdminMiddleware.php`<br>- `app/Policies/BookPolicy.php`<br>- `app/Providers/AuthServiceProvider.php`<br>- `routes/web.php`<br>- `tests/Feature/BookPermissionTest.php` | Middleware (`middlewareFor`), Authorization (Gates & Policies), Role Check |

---

## 8. Quy ước làm việc với Git của nhóm

1. **Nguyên tắc phân nhánh:**
   - Nhánh `main`: Chỉ chứa mã nguồn ổn định nhất đã được kiểm thử và hợp nhất.
   - Mỗi thành viên làm việc trên nhánh chức năng riêng (VD: `feature/books-backend`, `feature/books-search-filter`, `feature/books-trash`, `feature/books-ui`, `hoangdinh`, `thuyduy`, `yk`, `loc-va-phan-trang`...).
2. **Quy tắc thêm/sửa cơ sở dữ liệu:**
   - Tuyệt đối không chỉnh sửa cấu trúc CSDL bằng tay trong phpMyAdmin.
   - Tuyệt đối không sửa trực tiếp vào các file migration cũ đã chạy.
   - Nếu cần thay đổi hoặc thêm cột/bảng mới, bắt buộc tạo migration mới và thông báo cho Trưởng nhóm (TV1).
3. **Quy trình đẩy mã nguồn và kiểm tra trước khi Commit:**
   - Trước khi bắt đầu làm việc: `git pull origin main`.
   - Chạy `vendor/bin/pint --dirty` để chuẩn hóa định dạng code và chạy `php artisan test` để đảm bảo không làm gãy chức năng hiện có.
   - Sau khi hoàn thành chức năng trên nhánh riêng, tạo Pull Request (PR) để Trưởng nhóm review và merge vào `main`.

---

## 9. Bảng tổng hợp các lỗi thực tế và cách xử lý

Bảng ghi nhận các lỗi kỹ thuật thực tế nhóm đã gặp phải trong quá trình thiết lập và giải pháp xử lý triệt để:

| STT | Hiện tượng lỗi | Nguyên nhân gốc rễ | Hướng giải quyết triệt để |
|:---:|---|---|---|
| **1** | Ảnh bìa mẫu từ link ngoài (Google Drive) bị lỗi hoặc tải chậm trên máy thành viên mới | Phụ thuộc vào đường dẫn ảnh bên ngoài hoặc thư mục `storage/app/public/` bị `.gitignore` chặn | Lưu toàn bộ 58 ảnh bìa gốc vào `database/data/covers/{id}.jpg` (được Git theo dõi 100%) và để `BookSeeder` tự động chép sang `storage/app/public/covers/` khi chạy `migrate --seed`. |
| **2** | Chạy lại `php artisan db:seed` lần thứ 2 báo lỗi *Duplicate entry for key PRIMARY* | Các Seeder (`CategorySeeder`, `AuthorSeeder`, `PublisherSeeder`, `BookSeeder`) dùng `insert()` với `id` cố định từ 1 | Sử dụng lệnh `php artisan migrate:fresh --seed` khi muốn làm sạch và nạp lại toàn bộ dữ liệu mẫu từ đầu. |
| **3** | Lệnh `php -v` hoặc `composer` nhận nhầm phiên bản PHP cũ của XAMPP thay vì PHP 8.4 | Biến môi trường hệ thống (`PATH`) đang ưu tiên trỏ về thư mục PHP cũ | Cập nhật lại thứ tự biến `PATH` đưa đường dẫn PHP 8.4 lên đầu, dùng đúng một phiên bản PHP thống nhất. |
| **4** | Lỗi *could not find driver* hoặc *PDOException: connection refused* khi chạy migration | PHP chưa bật extension `pdo_mysql` hoặc dịch vụ MySQL trong XAMPP chưa được khởi động | Bật dòng `extension=pdo_mysql` trong file `php.ini` và mở XAMPP Control Panel bấm **Start MySQL**. |
| **5** | Lỗi *Table 'book_store_db.sessions' doesn't exist* khi duyệt website | Cấu hình trong `.env` đặt `SESSION_DRIVER=database` nhưng CSDL chưa có bảng lưu trữ session | Chạy `php artisan migrate` để tạo đầy đủ các bảng hệ thống (`sessions`, `cache`, `jobs`) của Laravel. |
| **6** | Ảnh bìa sách không hiển thị trên giao diện web (báo lỗi 404) | Thư mục `public/storage` chưa được liên kết với `storage/app/public` | Chạy lệnh `php artisan storage:link`. |
| **7** | Chạy PHPUnit báo lỗi tại các bài test upload ảnh bìa (`UploadedFile::fake()->image()`) | Môi trường PHP CLI chưa bật tiện ích mở rộng xử lý ảnh `gd` | Mở file `php.ini`, bỏ dấu `;` ở dòng `extension=gd` và `extension=pdo_sqlite`. |

---

## 10. Kết luận và hướng phát triển

### 10.1. Kết quả đạt được
- Xây dựng thành công ứng dụng quản lý đầu sách hoàn chỉnh theo chuẩn kiến trúc MVC trên nền tảng **Laravel 13** và **PHP 8.4**.
- Chuẩn hóa cơ sở dữ liệu đạt chuẩn 3NF với các bảng thực thể và bảng trung gian nhiều - nhiều (`author_book`, `book_category`), hỗ trợ xóa mềm (Soft Delete) và quản lý thùng rác.
- Nạp sẵn bộ dữ liệu mẫu thực tế gồm **58 cuốn sách tiếng Việt**, **68 tác giả**, **6 thể loại**, **20 nhà xuất bản**, đầy đủ thông tin xuất bản, giá bán, tồn kho và ảnh bìa phục vụ nội bộ.
- Xây dựng bộ kiểm thử tự động (Feature Tests) đảm bảo độ tin cậy cho các chức năng phân quyền, lọc dữ liệu, kiểm tra biểu mẫu và quản lý thùng rác.

### 10.2. Hướng mở rộng tiếp theo
- Bổ sung giao diện CRUD riêng cho danh mục Thể loại (`categories`), Tác giả (`authors`) và Nhà xuất bản (`publishers`).
- Đồng bộ hóa việc sử dụng `BookPolicy` và `@can` trên toàn bộ Controller và Blade View.
- Xây dựng giao diện giỏ hàng và đặt sách trực tuyến cho độc giả.
- Xây dựng hệ thống RESTful API phục vụ kết nối với ứng dụng di động (Mobile App).
