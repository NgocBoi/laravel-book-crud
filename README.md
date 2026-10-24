# TÀI LIỆU HƯỚNG DẪN KỸ THUẬT: CÀI ĐẶT VÀ PHÁT TRIỂN ỨNG DỤNG WEB VỚI LARAVEL
## Bài thực hành: Quản lý đầu sách (CRUD) — Mô hình MVC

> **Học phần:** Phát triển ứng dụng web với mã nguồn mở  
> **Đơn vị:** Khoa Công nghệ thông tin — Trường Kỹ thuật và Công nghệ — Đại học Trà Vinh  
> **Lớp:** DA23TTD — **Nhóm thực hiện:** Nhóm 3  
> **Trưởng nhóm:** Huỳnh Thị Ngọc Bội (MSSV: 110123071)  
> **Nền tảng kỹ thuật:** Laravel 13, PHP 8.4.x, Composer 2.x, MySQL (XAMPP), Bootstrap 5

---

## Mục lục

1. [Giới thiệu và mục tiêu dự án](#1-giới-thiệu-và-mục-tiêu-dự-án)
   - 1.1. Phạm vi dự án
   - 1.2. Kết quả đầu ra yêu cầu
   - 1.3. Luồng xử lý chuẩn theo mô hình MVC
2. [Hướng dẫn thiết lập và khởi chạy dự án cho thành viên nhóm](#2-hướng-dẫn-thiết-lập-và-khởi-chạy-dự-án-cho-thành-viên-nhóm)
   - 2.1. Quy trình thiết lập nhanh
   - 2.2. Tài khoản kiểm thử hệ thống
3. [Chuẩn bị môi trường và kiểm tra cài đặt](#3-chuẩn-bị-môi-trường-và-kiểm-tra-cài-đặt)
   - 3.1. Bảng yêu cầu môi trường
   - 3.2. Kiểm tra phiên bản công cụ
   - 3.3. Cấu hình extension PHP 8.4
4. [Nhận diện cấu trúc thư mục của dự án](#4-nhận-diện-cấu-trúc-thư-mục-của-dự-án)
5. [Thiết kế cơ sở dữ liệu và cấu hình kết nối](#5-thiết-kế-cơ-sở-dữ-liệu-và-cấu-hình-kết-nối)
   - 5.1. Sơ đồ quan hệ thực thể (ERD 3NF)
   - 5.2. Đặc tả chi tiết các bảng dữ liệu
   - 5.3. Tạo cơ sở dữ liệu và cấu hình file `.env`
6. [Xây dựng chức năng CRUD theo mô hình MVC](#6-xây-dựng-chức-năng-crud-theo-mô-hình-mvc)
   - 6.1. Định tuyến (Routes)
   - 6.2. Bộ điều khiển (Controllers)
   - 6.3. Mô hình dữ liệu (Models & Eloquent Relationships)
   - 6.4. Giao diện người dùng (Blade Views & Bootstrap 5)
   - 6.5. Quy ước xử lý ảnh bìa và số lượng kho
7. [Phân công nhiệm vụ nhóm 8 thành viên](#7-phân-công-nhiệm-vụ-nhóm-8-thành-viên)
8. [Quy ước làm việc với Git của nhóm](#8-quy-ước-làm-việc-với-git-của-nhóm)
9. [Bảng tổng hợp các lỗi thực tế và cách xử lý](#9-bảng-tổng-hợp-các-lỗi-thực-tế-và-cách-xử-lý)
10. [Kết luận và hướng phát triển](#10-kết-luận-và-hướng-phát-triển)

---

## 1. Giới thiệu và mục tiêu dự án

### 1.1. Phạm vi dự án
Tài liệu hướng dẫn thiết lập môi trường phát triển, khởi tạo và cấu trúc dự án ứng dụng web với framework **Laravel 13 (PHP 8.4)**, kết nối hệ quản trị cơ sở dữ liệu **MySQL**, nhận diện vai trò các thành phần trong kiến trúc MVC và phát triển hoàn chỉnh chức năng CRUD (Create - Read - Update - Delete) cho hệ thống **Quản lý đầu sách (Book Management)**.

Đối với nhóm đông người (8 thành viên), hệ thống mở rộng thêm cơ chế phân quyền (Administrator & User), tách chuẩn hóa dữ liệu Thể loại (`categories`), Tác giả (`authors`), Nhà xuất bản (`publishers`), dịch vụ lọc nâng cao (`BookFilterService`) và nghiệp vụ kiểm soát tồn kho (`stock_quantity`).

### 1.2. Kết quả đầu ra yêu cầu
- Khởi chạy thành công ứng dụng trên môi trường cục bộ (`localhost`).
- Nhận diện và giải thích rõ vai trò của Route, Controller, Model, View, Form Request và Migration.
- Thực hiện đầy đủ các thao tác xem danh sách, xem chi tiết, thêm mới, chỉnh sửa, xóa mềm (Soft Delete) và kiểm tra tính hợp lệ của dữ liệu đầu vào (Validation).
- Toàn bộ mã nguồn được quản lý phiên bản trên GitHub, có tài liệu hướng dẫn và dữ liệu mẫu thực tế (58 cuốn sách kèm ảnh bìa).

### 1.3. Luồng xử lý chuẩn theo mô hình MVC

```text
  [ Trình duyệt (Browser / Client) ]
                 │
                 │ 1. Gửi HTTP Request (VD: GET /books)
                 ▼
     [ Route (routes/web.php) ]
                 │
                 │ 2. Khớp URI và gọi Action tương ứng
                 ▼
  [ Controller (app/Http/Controllers) ]
                 │
                 │ 3. Gọi Model truy vấn dữ liệu
                 ▼
    [ Model Eloquent (app/Models) ]
                 │
                 │ 4. Thực thi câu lệnh SQL (ORM)
                 ▼
     [ Cơ sở dữ liệu (MySQL) ]
                 │
                 │ 5. Trả dữ liệu bản ghi về Model
                 ▼
  [ Controller (app/Http/Controllers) ]
                 │
                 │ 6. Xử lý logic và truyền dữ liệu sang View
                 ▼
    [ View Blade (resources/views) ]
                 │
                 │ 7. Render mã HTML hoàn chỉnh
                 ▼
  [ Trình duyệt (Browser / Client) ]
```

---

## 2. Hướng dẫn thiết lập và khởi chạy dự án cho thành viên nhóm

Mục này dành riêng cho các thành viên trong nhóm sau khi clone mã nguồn từ GitHub về máy cá nhân để thiết lập môi trường chạy được ngay trong vài phút.

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
4. Kiểm tra file `.env` đảm bảo thông số kết nối khớp:
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
*(Lệnh này tạo liên kết từ `public/storage` sang `storage/app/public` để trình duyệt web hiển thị được 58 ảnh bìa sách).*

#### Bước 6. Tạo bảng và nạp dữ liệu mẫu
```bash
php artisan migrate --seed
```
Lệnh này sẽ tự động khởi tạo toàn bộ cấu trúc bảng và nạp sẵn **6 thể loại**, **58 tác giả**, **20 nhà xuất bản**, **58 đầu sách thực tế** và **2 tài khoản kiểm thử**.

#### Bước 7. Khởi chạy máy chủ nội bộ
```bash
php artisan serve
```
Mở trình duyệt truy cập: `http://127.0.0.1:8000` hoặc `http://localhost:8000`.

> **Ghi chú về đường dẫn PHP:** Nếu trên máy bạn có nhiều phiên bản PHP và lệnh `php` chưa trỏ về PHP 8.4, hãy thay thế `php` bằng đường dẫn thực tế, ví dụ: `C:\xampp\php_new\php.exe artisan ...`

### 2.2. Tài khoản kiểm thử hệ thống

Sau khi chạy lệnh seed, hệ thống cung cấp sẵn 2 tài khoản phân quyền để kiểm thử:

| Vai trò | Địa chỉ Email | Mật khẩu | Quyền hạn |
|---|---|---|---|
| **Quản trị viên (Admin)** | `admin@example.com` | `password` | Toàn quyền thêm, sửa, xóa, nhập kho, quản trị danh mục |
| **Nhân viên / Bạn đọc (User)** | `user@example.com` | `password` | Xem danh sách, tìm kiếm, lọc, xem chi tiết sách |

---

## 3. Chuẩn bị môi trường và kiểm tra cài đặt

### 3.1. Bảng yêu cầu môi trường

| Thành phần | Phiên bản yêu cầu | Mục đích sử dụng | Ghi chú |
|---|---|---|---|
| **Hệ điều hành** | Windows 10 / 11 | Môi trường phát triển cục bộ | Máy cá nhân của thành viên |
| **PHP** | 8.4.x (tối thiểu >= 8.3) | Thông dịch và thực thi mã nguồn Laravel | Bật đủ các extension cần thiết |
| **Composer** | 2.x | Quản lý các gói thư viện mã nguồn PHP | Chạy đồng bộ cùng phiên bản PHP |
| **Hệ quản trị CSDL** | MySQL / MariaDB (XAMPP) | Lưu trữ cơ sở dữ liệu `book_store_db` | Khởi động dịch vụ qua XAMPP Control Panel |
| **Git for Windows** | Phiên bản mới nhất | Quản lý mã nguồn, tạo nhánh và commit | Kết nối với GitHub repository |
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
extension=mbstring
extension=openssl
extension=pdo_mysql
extension=zip
```

---

## 4. Nhận diện cấu trúc thư mục của dự án

Bảng phân tích vai trò các thư mục và tập tin cốt lõi trong dự án theo chuẩn kiến trúc Laravel:

| Thư mục / Tập tin | Vai trò trong hệ thống | Thành phần MVC |
|---|---|:---:|
| `app/Models/` | Chứa các lớp thực thể dữ liệu Eloquent ORM (`Book`, `Category`, `Author`, `Publisher`, `User`), định nghĩa quan hệ và quy tắc truy vấn | **Model** |
| `app/Http/Controllers/` | Chứa các lớp điều khiển tiếp nhận yêu cầu từ Route, tương tác với Model và trả về kết quả sang View | **Controller** |
| `app/Http/Requests/` | Chứa các lớp Form Request phục vụ xác thực dữ liệu đầu vào (Validation) độc lập | **Request** |
| `app/Services/` | Chứa các lớp xử lý nghiệp vụ chuyên biệt (ví dụ `BookFilterService` lọc sách) | **Service** |
| `resources/views/` | Chứa các tệp giao diện sử dụng Blade Template Engine, hiển thị dữ liệu cho người dùng | **View** |
| `routes/web.php` | Định nghĩa toàn bộ danh sách điểm cuối URL và ánh xạ tới phương thức Controller | **Route** |
| `database/migrations/` | Chứa các file định nghĩa cấu trúc bảng CSDL theo lịch sử phiên bản | **Migration** |
| `database/seeders/` | Chứa các file nạp dữ liệu mẫu ban đầu cho CSDL | **Seeder** |
| `database/data/` | Lưu trữ file dữ liệu mẫu chuẩn hóa (`books.json`) để Git theo dõi đồng bộ | **Data Asset** |
| `storage/app/public/books/` | Lưu trữ các tập tin ảnh bìa sách tải lên hệ thống | **Storage** |
| `public/` | Thư mục công khai của web server, chứa `index.php` và symlink `storage` | **Public** |
| `.env` | Tập tin cấu hình môi trường cục bộ (không đưa lên GitHub) | **Config** |

---

## 5. Thiết kế cơ sở dữ liệu và cấu hình kết nối

### 5.1. Sơ đồ quan hệ thực thể (ERD Chuẩn 3NF)

```text
    ┌──────────────┐                 ┌────────────────────┐
    │  categories  │1               N│       books        │
    │──────────────│─────────────────│────────────────────│
    │ id (PK)      │                 │ id (PK)            │
    │ name         │                 │ category_id (FK)   │
    │ slug         │                 │ author_id (FK)     │
    └──────────────┘                 │ publisher_id (FK)  │
                                     │ book_code (Unique) │
    ┌──────────────┐1               N│ isbn               │
    │   authors    │─────────────────│ title              │
    │──────────────│                 │ price              │
    │ id (PK)      │                 │ stock_quantity     │
    │ name         │                 │ cover_image        │
    │ slug         │                 │ deleted_at (Soft)  │
    └──────────────┘                 └────────────────────┘
                                               │N
    ┌──────────────┐1                          │
    │  publishers  │───────────────────────────┘
    │──────────────│
    │ id (PK)      │
    │ name         │
    │ slug         │
    └──────────────┘
```

### 5.2. Đặc tả chi tiết các bảng dữ liệu

#### Bảng `categories` (Thể loại sách)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Khóa chính thể loại |
| `name` | VARCHAR(150) | Unique, Not Null | Tên thể loại sách |
| `slug` | VARCHAR(150) | Unique, Not Null | Đường dẫn thân thiện SEO |
| `description` | TEXT | Nullable | Mô tả tóm tắt thể loại |
| `timestamps` | TIMESTAMP | Nullable | Thời gian tạo và cập nhật |

#### Bảng `authors` (Tác giả)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Khóa chính tác giả |
| `name` | VARCHAR(150) | Not Null | Họ tên tác giả |
| `slug` | VARCHAR(150) | Unique, Not Null | Đường dẫn định danh tác giả |
| `bio` | TEXT | Nullable | Tiểu sử tác giả |
| `avatar` | VARCHAR(255) | Nullable | Đường dẫn ảnh đại diện |
| `timestamps` | TIMESTAMP | Nullable | Thời gian tạo và cập nhật |

#### Bảng `publishers` (Nhà xuất bản)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Khóa chính nhà xuất bản |
| `name` | VARCHAR(150) | Unique, Not Null | Tên nhà xuất bản |
| `slug` | VARCHAR(150) | Unique, Not Null | Đường dẫn định danh NXB |
| `email` | VARCHAR(100) | Nullable | Email liên hệ NXB |
| `phone` | VARCHAR(30) | Nullable | Số điện thoại liên hệ |
| `address` | VARCHAR(255) | Nullable | Địa chỉ trụ sở NXB |
| `description` | TEXT | Nullable | Giới thiệu nhà xuất bản |
| `timestamps` | TIMESTAMP | Nullable | Thời gian tạo và cập nhật |

#### Bảng `books` (Bảng trọng tâm)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Khóa chính đầu sách |
| `category_id` | BIGINT UNSIGNED | Foreign Key $\rightarrow$ `categories(id)` | Tham chiếu thể loại sách |
| `author_id` | BIGINT UNSIGNED | Foreign Key $\rightarrow$ `authors(id)` | Tham chiếu tác giả |
| `publisher_id`| BIGINT UNSIGNED | Foreign Key $\rightarrow$ `publishers(id)`| Tham chiếu nhà xuất bản |
| `book_code` | VARCHAR(50) | Unique, Not Null | Mã quản lý nội bộ (`BK-{ISBN}`) |
| `isbn` | VARCHAR(20) | Index, Nullable | Mã tiêu chuẩn quốc tế ISBN |
| `title` | VARCHAR(255) | Not Null | Nhan đề tác phẩm |
| `translator` | VARCHAR(255) | Nullable | Người dịch |
| `supplier` | VARCHAR(255) | Nullable | Nhà phát hành / Nhà cung cấp |
| `publish_year`| INT | Nullable | Năm xuất bản |
| `language` | VARCHAR(50) | Default: 'Tiếng Việt' | Ngôn ngữ phát hành |
| `weight` | INT | Nullable | Trọng lượng ấn phẩm (gram) |
| `dimensions` | VARCHAR(100) | Nullable | Kích thước (dài x rộng x dày) |
| `pages` | INT | Nullable | Số trang sách |
| `original_price`| DECIMAL(12,2)| Default: 0 | Giá bìa niêm yết (VNĐ) |
| `price` | DECIMAL(12,2)| Default: 0 | Giá bán thực tế (VNĐ) |
| `stock_quantity`| INT | Default: 0, Unsigned | Số lượng tồn kho thực tế |
| `sold_quantity` | INT | Default: 0, Unsigned | Số lượng đã bán |
| `cover_image` | VARCHAR(255) | Nullable | Tên tệp ảnh bìa lưu trữ |
| `description` | LONGTEXT | Nullable | Tóm tắt nội dung chi tiết |
| `is_trending` | BOOLEAN | Default: false | Đánh dấu sách xu hướng |
| `badge_type` | VARCHAR(50) | Nullable | Huy hiệu hiển thị (`best-seller`) |
| `timestamps` | TIMESTAMP | Nullable | Thời điểm tạo và sửa đổi |
| `deleted_at` | TIMESTAMP | Nullable, Index | Dấu thời gian xóa mềm (Soft Delete) |

#### Bảng `users` (Tài khoản & Phân quyền)
| Tên cột | Kiểu dữ liệu | Ràng buộc | Ý nghĩa |
|---|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment | Khóa chính người dùng |
| `name` | VARCHAR(255) | Not Null | Tên hiển thị người dùng |
| `email` | VARCHAR(255) | Unique, Not Null | Địa chỉ hòm thư đăng nhập |
| `password` | VARCHAR(255) | Not Null | Mật khẩu đã mã hóa (Bcrypt) |
| `role` | VARCHAR(30) | Default: 'user' | Vai trò: `admin` hoặc `user` |
| `remember_token`| VARCHAR(100)| Nullable | Token duy trì đăng nhập |
| `timestamps` | TIMESTAMP | Nullable | Thời điểm khởi tạo tài khoản |

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

### 6.1. Định tuyến (Routes)
Hệ thống sử dụng các Route chuẩn Resource trong `routes/web.php`:
```php
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\PublisherController;

Route::get('/', fn() => redirect()->route('books.index'));

// Quản lý sách
Route::resource('books', BookController::class);

// Quản lý các danh mục dữ liệu tham chiếu
Route::resource('categories', CategoryController::class);
Route::resource('authors', AuthorController::class);
Route::resource('publishers', PublisherController::class);
```

### 6.2. Bộ điều khiển (Controllers)
Bộ điều khiển triển khai đầy đủ 7 phương thức chuẩn RESTful:
- `index()`: Lấy danh sách kèm phân trang và nạp sẵn các quan hệ (`with(['category', 'author', 'publisher'])`).
- `create()`: Cung cấp danh sách thể loại, tác giả, NXB để hiển thị form thêm mới.
- `store(StoreBookRequest $request)`: Xác thực dữ liệu, xử lý lưu file ảnh bìa vào `storage/app/public/books` và tạo bản ghi mới.
- `show(Book $book)`: Hiển thị chi tiết ấn phẩm cùng danh sách các sách liên quan cùng thể loại.
- `edit(Book $book)`: Hiển thị form chỉnh sửa thông tin.
- `update(UpdateBookRequest $request, Book $book)`: Cập nhật thông tin và thay thế ảnh bìa an toàn (xóa ảnh cũ nếu có ảnh mới).
- `destroy(Book $book)`: Thực hiện xóa mềm (`$book->delete()`).

### 6.3. Mô hình dữ liệu (Models & Eloquent Relationships)
Các quan hệ dữ liệu được thiết lập chặt chẽ bằng Eloquent ORM:
- **`Category`**: `hasMany(Book::class)`
- **`Author`**: `hasMany(Book::class)`
- **`Publisher`**: `hasMany(Book::class)`
- **`Book`**:
  - `belongsTo(Category::class)`
  - `belongsTo(Author::class)`
  - `belongsTo(Publisher::class)`
  - Kích hoạt Trait: `use SoftDeletes;`
- **`User`**: Có hàm hỗ trợ kiểm tra vai trò:
  ```php
  public function isAdmin(): bool {
      return $this->role === 'admin';
  }
  ```

### 6.4. Giao diện người dùng (Blade Views & Bootstrap 5)
- Thư mục giao diện dùng chung: `resources/views/layouts/app.blade.php`.
- Thư mục giao diện sách: `resources/views/books/` (`index.blade.php`, `show.blade.php`, `create.blade.php`, `edit.blade.php`).
- Khối thành phần tái sử dụng (Partials): `resources/views/books/partials/` (`filter-bar.blade.php`, `stock-status.blade.php`).

### 6.5. Quy ước xử lý ảnh bìa và số lượng kho
1. **Lấy ảnh bìa:** Sử dụng helper URL có sẵn trong Model `Book`:
   ```blade
   <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}">
   ```
2. **Số lượng tồn kho:** Cột trong CSDL là `stock_quantity`. Model đã hỗ trợ alias đồng bộ: `$book->stock` hoặc `$book->quantity` đều trỏ thẳng về `stock_quantity`.

---

## 7. Phân công nhiệm vụ nhóm 8 thành viên

Mô hình phân rã module độc lập giúp 8 thành viên phát triển song song không gây xung đột trên Git:

| TT | Thành viên & Module | Nhiệm vụ chính | Các file phụ trách riêng (Không đụng người khác) | Kiến thức Laravel áp dụng |
|:---:|---|---|---|---|
| **TV1** | **Cơ sở dữ liệu & Model** | - Thiết kế migration cho `books`, `categories`, `authors`, `publishers`<br>- Bổ sung `stock_quantity` vào sách và `role` vào users<br>- Thiết lập khóa ngoại, quan hệ Eloquent và dữ liệu mẫu Seeders | - `database/migrations/...`<br>- `database/seeders/...`<br>- `database/data/books.json`<br>- `app/Models/Book.php`, `Category.php`<br>- `app/Models/Author.php`, `Publisher.php`, `User.php` | Migration, Foreign Key, Eloquent Relationships (`hasMany`, `belongsTo`), Database Seeding |
| **TV2** | **Quản lý Thể loại** | - Xem danh sách thể loại<br>- CRUD Thể loại (Thêm, sửa, xóa)<br>- Kiểm tra ràng buộc thể loại đang có sách trước khi xóa (`$category->books()->exists()`) | - `app/Http/Controllers/CategoryController.php`<br>- `app/Http/Requests/StoreCategoryRequest.php`<br>- `resources/views/categories/` | CRUD Controller, Form Request, Eloquent Relationship Check |
| **TV3** | **Quản lý Tác giả & NXB** | - CRUD Tác giả (`authors`)<br>- CRUD Nhà xuất bản (`publishers`)<br>- Kiểm tra ràng buộc dữ liệu trước khi xóa bản ghi đang có sách tham chiếu<br>- Cung cấp danh sách cho form sách | - `app/Http/Controllers/AuthorController.php`<br>- `app/Http/Controllers/PublisherController.php`<br>- `resources/views/authors/`<br>- `resources/views/publishers/` | Dual CRUD Controllers, Resource Views, Delete Constraints |
| **TV4** | **Backend CRUD Sách** | - Xử lý danh sách, chi tiết, thêm, sửa, xóa sách ở tầng backend<br>- Viết Validation cho dữ liệu sách<br>- Xử lý upload và lưu trữ ảnh bìa trong storage<br>- Đảm bảo liên kết đúng các khóa ngoại | - `app/Http/Controllers/BookController.php`<br>- `app/Http/Requests/StoreBookRequest.php`<br>- `app/Http/Requests/UpdateBookRequest.php` | Controller Resource, Form Request, Storage File Upload, Transaction |
| **TV5** | **Giao diện Quản lý Sách** | - Thiết kế giao diện danh sách, chi tiết, thêm mới và cập nhật sách<br>- Hiển thị ảnh bìa, giá, thể loại, tác giả, NXB và số lượng tồn<br>- Hiển thị thông báo, lỗi validation và trạng thái hết hàng | - `resources/views/books/index.blade.php`<br>- `resources/views/books/show.blade.php`<br>- `resources/views/books/create.blade.php`<br>- `resources/views/books/edit.blade.php`<br>- `resources/views/books/partials/` | Blade Templates, Bootstrap 5 UI, Component Reusability |
| **TV6** | **Tìm kiếm, Lọc & Phân trang** | - Tìm kiếm theo tên sách<br>- Lọc theo thể loại, tác giả, nhà xuất bản, khoảng giá<br>- Sắp xếp theo giá / thời gian tạo, phân trang giữ query string<br>*(Bàn giao service & partial cho TV4, TV5 tích hợp)* | - `app/Services/BookFilterService.php`<br>- `resources/views/books/partials/filter-bar.blade.php` | Service Pattern, Eloquent Query Scope, `when()`, Pagination `withQueryString()` |
| **TV7** | **Đăng nhập & Phân quyền** | - Xử lý đăng nhập, đăng xuất<br>- Thiết lập Middleware / Policy chặn User truy cập trái phép các thao tác sửa đổi<br>- Ẩn/hiện nút chức năng theo quyền (`role === 'admin'`) | - `app/Http/Controllers/AuthController.php`<br>- `app/Http/Middleware/AdminMiddleware.php`<br>- `app/Policies/BookPolicy.php`<br>- `resources/views/auth/` | Authentication, Middleware, Authorization (Gates & Policies), Blade `@can` |
| **TV8** | **Quản lý Tồn kho** | - Xem số lượng tồn của từng đầu sách<br>- Form / Modal nhập thêm số lượng kho (cộng dồn an toàn `increment('stock_quantity', $qty)`)<br>- Cảnh báo: Sắp hết (< 5), Hết hàng (= 0)<br>- Phân quyền chỉ Admin được nhập kho | - `app/Http/Controllers/BookStockController.php`<br>- `app/Http/Requests/UpdateBookStockRequest.php`<br>- `resources/views/stock/`<br>- `resources/views/books/partials/stock-status.blade.php` | Eloquent `increment()`, Business Logic tồn kho, Badge động, Security Check |

---

## 8. Quy ước làm việc với Git của nhóm

1. **Nguyên tắc phân nhánh:**
   - Nhánh `main`: Chỉ chứa mã nguồn ổn định nhất đã được kiểm thử.
   - Mỗi thành viên tạo một nhánh chức năng riêng mang tên nhiệm vụ của mình:
     - `feature/categories` (TV2)
     - `feature/authors-publishers` (TV3)
     - `feature/books-backend` (TV4)
     - `feature/books-ui` (TV5)
     - `feature/books-filter` (TV6)
     - `feature/auth-permission` (TV7)
     - `feature/stock-management` (TV8)
2. **Quy tắc thêm/sửa cơ sở dữ liệu:**
   - Tuyệt đối không chỉnh sửa cấu trúc CSDL bằng tay trong phpMyAdmin.
   - Tuyệt đối không sửa trực tiếp vào các file migration cũ đã chạy.
   - Nếu cần thay đổi hoặc thêm cột/bảng mới, bắt buộc tạo migration mới và thông báo cho Trưởng nhóm (TV1).
3. **Quy trình đẩy mã nguồn:**
   - Trước khi bắt đầu làm việc: `git pull origin main`.
   - Sau khi hoàn thành chức năng trên nhánh riêng, tạo Pull Request (PR) để Trưởng nhóm review và merge vào `main`.

---

## 9. Bảng tổng hợp các lỗi thực tế và cách xử lý

Bảng ghi nhận các lỗi kỹ thuật thực tế nhóm đã gặp phải trong quá trình thiết lập và giải pháp xử lý triệt để:

| STT | Hiện tượng lỗi | Nguyên nhân gốc rễ | Hướng giải quyết triệt để |
|:---:|---|---|---|
| **1** | Chạy `artisan db:seed` báo lỗi: *Không tìm thấy file books.json* trên máy thành viên mới | File `.gitignore` mặc định trong `storage/app/public/` chứa quy tắc `*` chặn toàn bộ file trong thư mục, khiến `books.json` và ảnh không được đẩy lên GitHub | 1. Di chuyển file dữ liệu về thư mục chuẩn `database/data/books.json` (thư mục được Git theo dõi 100%).<br>2. Cập nhật `storage/app/public/.gitignore` thêm ngoại lệ `!books.json` và `!books/**`. |
| **2** | Lỗi vi phạm khóa duy nhất khi seed: *Duplicate entry for key books_isbn_unique* | Trong file dữ liệu mẫu có 2 cuốn sách khác nhau (*Kẻ Cướp Bến Bỏi* và *Perman Tập 4*) bị gán trùng mã ISBN | Chuẩn hóa lại file dữ liệu, cấp mã ISBN độc lập duy nhất cho từng cuốn sách trước khi nạp vào CSDL. |
| **3** | Lệnh `php -v` hoặc `composer` nhận nhầm PHP 8.2 của XAMPP thay vì PHP 8.4 | Biến môi trường hệ thống (`PATH`) đang ưu tiên trỏ về thư mục `C:\xampp\php` cũ của XAMPP | Cập nhật lại thứ tự biến `PATH` đưa đường dẫn PHP 8.4 lên đầu, hoặc gọi trực tiếp bằng đường dẫn tuyệt đối `C:\xampp\php_new\php.exe`. |
| **4** | Lỗi *could not find driver* hoặc *PDOException: connection refused* khi chạy migration | PHP chưa bật extension `pdo_mysql` hoặc dịch vụ MySQL trong XAMPP chưa được khởi động | Bật dòng `extension=pdo_mysql` trong file `php.ini` và mở XAMPP Control Panel bấm **Start MySQL**. |
| **5** | Lỗi *Table 'book_store_db.sessions' doesn't exist* khi duyệt website | Cấu hình trong `.env` đặt `SESSION_DRIVER=database` nhưng CSDL chưa có bảng lưu trữ session | Chạy `php artisan migrate` để tạo đầy đủ các bảng hệ thống của Laravel. |
| **6** | Ảnh bìa sách không hiển thị trên giao diện web (báo lỗi 404) | Thư mục `public/storage` chưa được liên kết với `storage/app/public` | Chạy lệnh `php artisan storage:link`. Nếu Windows báo lỗi phân quyền, mở CMD dưới quyền **Administrator** để tạo symlink. |

---

## 10. Kết luận và hướng phát triển

### 10.1. Kết quả đạt được
- Xây dựng thành công ứng dụng quản lý đầu sách hoàn chỉnh theo chuẩn kiến trúc MVC trên nền tảng **Laravel 13** và **PHP 8.4**.
- Chuẩn hóa cơ sở dữ liệu đạt chuẩn 3NF với 5 bảng liên kết khóa ngoại chặt chẽ, hỗ trợ xóa mềm (Soft Delete).
- Nạp sẵn bộ dữ liệu mẫu thực tế gồm **58 cuốn sách tiếng Việt**, đầy đủ thông tin metadata, giá bìa, tồn kho và ảnh bìa chất lượng cao.
- Phân chia nhiệm vụ độc lập, minh bạch và cân bằng cho nhóm 8 thành viên.

### 10.2. Hướng mở rộng tiếp theo
- Xây dựng giao diện giỏ hàng và đặt sách trực tuyến cho độc giả.
- Tích hợp cổng thanh toán trực tuyến (VNPAY / MoMo).
- Xây dựng hệ thống RESTful API phục vụ kết nối với ứng dụng di động (Mobile App).
- Tích hợp tính năng xuất/nhập danh mục sách bằng file Excel sử dụng `maatwebsite/excel`.
