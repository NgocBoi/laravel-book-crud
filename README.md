# Hướng dẫn thiết lập môi trường và khởi chạy Laravel — Book Management

> Tài liệu dành cho thành viên nhóm phát triển dự án **Book Management** trên Windows, sử dụng XAMPP, PHP 8.4, Composer, Laravel và MySQL.
>
> **Quy ước:** Dự án đang sử dụng Laravel 13 và PHP 8.4.x. Trước khi đổi phiên bản, cả nhóm cần thống nhất và cập nhật tài liệu này. Các đường dẫn dưới đây dựa trên máy mẫu; nếu máy của bạn khác, hãy thay bằng đường dẫn thực tế.

## Mục lục

1. [Môi trường cần có](#1-môi-trường-cần-có)
2. [Cài đặt XAMPP](#2-cài-đặt-xampp)
3. [Cài đặt PHP 8.4](#3-cài-đặt-php-84)
4. [Cài đặt Composer](#4-cài-đặt-composer)
5. [Kiểm tra môi trường trước khi làm](#5-kiểm-tra-môi-trường-trước-khi-làm)
6. [Tạo dự án Laravel mới](#6-tạo-dự-án-laravel-mới)
7. [Cấu hình database](#7-cấu-hình-database)
8. [Thành viên clone repository](#8-thành-viên-clone-repository)
9. [Chạy dự án hằng ngày](#9-chạy-dự-án-hằng-ngày)
10. [Lỗi thường gặp và cách xử lý](#10-lỗi-thường-gặp-và-cách-xử-lý)
11. [Nhật ký xử lý lỗi bảng `sessions`](#11-nhật-ký-xử-lý-lỗi-bảng-sessions)
12. [Quy ước Git của nhóm](#12-quy-ước-git-của-nhóm)
13. [Checklist thành viên mới](#13-checklist-thành-viên-mới)
14. [Liên kết chính thức](#14-liên-kết-chính-thức)

---

## 1. Môi trường cần có

| Công cụ | Mục đích | Ghi chú |
|---|---|---|
| Windows 10/11 | Hệ điều hành | Máy cá nhân của thành viên |
| Git for Windows | Clone, pull, commit, push | Cần quyền truy cập repository |
| XAMPP | MySQL/MariaDB và phpMyAdmin | Apache không bắt buộc khi dùng `artisan serve` |
| PHP 8.4.x | Chạy Laravel và Composer | Nhóm nên thống nhất cùng phiên bản |
| Composer 2.x | Quản lý thư viện PHP | Phải chạy bằng PHP phù hợp |
| VS Code | Viết code | Khuyến nghị |
| Node.js/npm | Build frontend bằng Vite | Chỉ cần nếu repository dùng `package.json` |

### Đường dẫn mẫu

Máy của người khởi tạo dự án có:

```text
C:\xampp\php\php.exe          PHP 8.2.12 (PHP mặc định trước đó)
C:\xampp\php_new\php.exe     PHP 8.4.26 (dùng chạy Laravel)
C:\xampp\htdocs\book-management
```

**Quan trọng:** Tạo thư mục `php_new` không tự thay PHP trong PATH. Khi nhập `php -v`, Windows có thể vẫn gọi PHP 8.2. Trong tài liệu này, lệnh dùng PHP 8.4 bằng đường dẫn đầy đủ để tránh nhầm phiên bản.

---

## 2. Cài đặt XAMPP

1. Tải XAMPP từ <https://www.apachefriends.org/>.
2. Cài đặt, ví dụ tại `C:\xampp`.
3. Mở XAMPP Control Panel.
4. Khởi động **MySQL** khi cần dùng database.
5. Mở <http://localhost/phpmyadmin/> để xác nhận phpMyAdmin hoạt động.

Laravel trong hướng dẫn này chạy bằng:

```bat
C:\xampp\php_new\php.exe artisan serve
```

Vì vậy, không nhất thiết phải khởi động Apache để chạy ứng dụng trong môi trường phát triển. Chỉ khởi động Apache nếu nhóm cần cấu hình hoặc dùng dịch vụ đó.

---

## 3. Cài đặt PHP 8.4

### 3.1. Tải PHP

1. Mở <https://windows.php.net/download/>.
2. Chọn bản PHP 8.4 phù hợp Windows và kiến trúc máy (thường là x64).
3. Với mục đích chạy CLI, tải gói PHP phù hợp theo hướng dẫn chính thức của PHP.
4. Giải nén vào một thư mục cố định, ví dụ `C:\xampp\php_new`.

Không thay thế thư mục `C:\xampp\php` đang được XAMPP sử dụng nếu chưa biết cách cấu hình PHP cho Apache. Việc thay sai phiên bản có thể làm Apache lỗi DLL.

### 3.2. Tạo và cấu hình `php.ini`

Trong `C:\xampp\php_new`, nếu chỉ có `php.ini-development` và `php.ini-production`:

1. Sao chép `php.ini-development`.
2. Đổi tên bản sao thành `php.ini`.
3. Mở `C:\xampp\php_new\php.ini`.
4. Kiểm tra các cấu hình extension cần thiết. Tùy bản PHP, bỏ dấu `;` ở đầu dòng nếu extension đã có sẵn:

```ini
extension_dir = "ext"
extension=curl
extension=fileinfo
extension=mbstring
extension=mysqli
extension=openssl
extension=pdo_mysql
extension=zip
```

Tên extension là `zip`, không phải `unzip`. Chỉ bật extension khi file DLL tương ứng tồn tại trong thư mục `ext`.

### 3.3. Kiểm tra PHP

Mở CMD mới và chạy:

```bat
C:\xampp\php_new\php.exe -v
C:\xampp\php_new\php.exe --ini
C:\xampp\php_new\php.exe -m
```

Kết quả cần xác nhận:

- Phiên bản là PHP 8.4.x.
- File cấu hình được tải là `C:\xampp\php_new\php.ini`.
- Danh sách module có các extension Laravel yêu cầu, đặc biệt `openssl`, `mbstring`, `pdo_mysql` và `zip`.

Để kiểm tra nhanh một số extension:

```bat
C:\xampp\php_new\php.exe -m | findstr /I "zip openssl mbstring pdo_mysql"
```

### 3.4. PHP PATH (tùy chọn)

Nếu muốn lệnh `php` gọi PHP 8.4 mặc định:

1. Mở **Environment Variables** của Windows.
2. Chỉnh biến `Path`.
3. Đưa `C:\xampp\php_new` lên trước `C:\xampp\php`.
4. Đóng CMD/PowerShell cũ và mở cửa sổ mới.
5. Kiểm tra:

```bat
where php
php -v
```

Cách này chỉ thay PHP CLI được gọi qua PATH; không nhất thiết thay PHP mà Apache của XAMPP sử dụng. Thành viên không bắt buộc chỉnh PATH nếu luôn dùng đường dẫn đầy đủ.

---

## 4. Cài đặt Composer

### 4.1. Tải và cài

1. Mở <https://getcomposer.org/download/>.
2. Tải `Composer-Setup.exe` cho Windows.
3. Chạy trình cài đặt.
4. Khi được hỏi đường dẫn PHP executable, chọn:

```text
C:\xampp\php_new\php.exe
```

5. Hoàn tất cài đặt.
6. Đóng và mở lại terminal.

### 4.2. Kiểm tra Composer

```bat
composer --version
where composer
```

Nếu cài bằng Composer Windows Installer, file PHAR thường nằm tại:

```text
C:\ProgramData\ComposerSetup\bin\composer.phar
```

Đây là đường dẫn mẫu, không đảm bảo giống trên mọi máy. Kiểm tra file có tồn tại trước khi dùng.

Kiểm tra Composer bằng PHP 8.4:

```bat
C:\xampp\php_new\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar --version
```

Nếu PHAR ở nơi khác, thay bằng đường dẫn thực tế. Nếu Composer đã được cài và cấu hình chắc chắn dùng PHP 8.4, có thể chạy `composer` bình thường; khi gặp lỗi phiên bản PHP, ưu tiên gọi Composer bằng đường dẫn PHP rõ ràng.

---

## 5. Kiểm tra môi trường trước khi làm

Chạy các lệnh sau:

```bat
C:\xampp\php_new\php.exe -v
C:\xampp\php_new\php.exe --ini
C:\xampp\php_new\php.exe -m | findstr /I "zip openssl mbstring pdo_mysql"
composer --version
where composer
```

Nếu bất kỳ bước nào sai, xử lý môi trường trước khi tạo dự án hoặc cài thư viện.

---

## 6. Tạo dự án Laravel mới

> Chỉ thực hiện mục này khi bạn là người tạo dự án ban đầu. Thành viên clone repository đã có mã nguồn phải làm theo mục 8, không chạy `create-project`.

### 6.1. Tạo thư mục dự án

```bat
cd /d C:\xampp\htdocs
mkdir book-management
cd book-management
```

Thư mục đích cần trống trước khi tạo dự án.

### 6.2. Tạo Laravel 13

Nếu nhóm đã thống nhất Laravel 13 và PHP 8.4:

```bat
C:\xampp\php_new\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar create-project laravel/laravel . "^13.0"
```

Lệnh trên giả định Composer PHAR ở đường dẫn mẫu. Nếu Composer nằm nơi khác, thay bằng đường dẫn chính xác. Nếu Composer được cấu hình đúng với PHP 8.4, có thể dùng:

```bat
composer create-project laravel/laravel . "^13.0"
```

Không chạy lệnh này trong một thư mục đã chứa repository hoặc mã nguồn quan trọng.

### 6.3. Kiểm tra dự án

```bat
C:\xampp\php_new\php.exe artisan --version
```

Nếu `.env` chưa có khóa ứng dụng, tạo bằng:

```bat
C:\xampp\php_new\php.exe artisan key:generate
```

Không cần tạo lại APP_KEY nếu file `.env` đã có khóa hợp lệ, trừ khi bạn chủ động muốn đổi khóa cho môi trường local mới.

### 6.4. Chạy website

```bat
C:\xampp\php_new\php.exe artisan serve
```

Mở địa chỉ terminal hiển thị, thường là <http://127.0.0.1:8000/>. Dừng server bằng `Ctrl + C`.

---

## 7. Cấu hình database

### 7.1. Tạo database

1. Mở XAMPP Control Panel.
2. Start **MySQL**.
3. Mở <http://localhost/phpmyadmin/>.
4. Tạo database tên `book_store_db`.

### 7.2. Cấu hình `.env`

Mở `.env` tại thư mục gốc dự án và kiểm tra:

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

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=database
```

Username, password và port phải phù hợp với cấu hình MySQL thực tế. `root` không mật khẩu là cấu hình mặc định thường gặp của XAMPP, không phải quy tắc cho mọi máy.

### 7.3. Chạy migration

```bat
C:\xampp\php_new\php.exe artisan config:clear
C:\xampp\php_new\php.exe artisan migrate
```

Laravel sẽ chạy các migration chưa được ghi nhận trong bảng `migrations`.

**Cảnh báo:** `migrate:fresh` xóa toàn bộ bảng rồi tạo lại. Không dùng trên database có dữ liệu cần giữ.

### 7.4. Bảng session, cache và queue

Với cấu hình:

```dotenv
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

ứng dụng cần các bảng database tương ứng cho session, cache và queue. Các migration mặc định của Laravel có thể tạo bảng cache và jobs; bảng `sessions` có thể cần migration riêng tùy phiên bản và trạng thái dự án.

Nếu lỗi nói bảng nào không tồn tại:

1. Kiểm tra database và tên bảng trong phpMyAdmin.
2. Kiểm tra migration trong `database/migrations`.
3. Chạy `artisan migrate:status`.
4. Chỉ tạo migration còn thiếu sau khi đã kiểm tra, tránh migration trùng.

Nếu dự án chưa cần session/cache trong database, nhóm có thể cân nhắc dùng cấu hình file/sync ở môi trường local, nhưng cần thống nhất trước khi đổi cấu hình dùng chung.

---

## 8. Thành viên clone repository

### 8.1. Chuẩn bị

Trước khi clone, cần:

- Git for Windows.
- PHP 8.4.x và extension cần thiết.
- Composer 2.x.
- Quyền truy cập repository.
- XAMPP/MySQL nếu dự án dùng MySQL.
- Node.js/npm nếu dự án dùng Vite/frontend build.

### 8.2. Clone repository

```bat
cd /d C:\xampp\htdocs
git clone <URL_REPOSITORY> book-management
cd book-management
```

Thay `<URL_REPOSITORY>` bằng URL thật của repository. Không nhập nguyên dấu `< >`.

Nếu thư mục đã tồn tại, hãy `cd` vào thư mục repository đó; không clone chồng lên một thư mục đang chứa mã nguồn.

### 8.3. Cài dependency

Nếu Composer PHAR nằm ở đường dẫn mẫu:

```bat
C:\xampp\php_new\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install
```

Hoặc dùng `composer install` nếu Composer chắc chắn chạy bằng PHP 8.4.

**Dùng `composer install`, không tự ý dùng `composer update`.** `install` cài phiên bản thư viện theo `composer.lock`, giúp các thành viên có môi trường nhất quán.

### 8.4. Tạo `.env`

Thông thường `.env` không được commit vì chứa cấu hình riêng của máy. Tạo từ file mẫu:

```bat
copy .env.example .env
```

Mở `.env` và đặt database tương ứng:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_store_db
DB_USERNAME=root
DB_PASSWORD=
```

Không commit `.env` hoặc thông tin bí mật lên Git.

### 8.5. Tạo khóa ứng dụng

```bat
C:\xampp\php_new\php.exe artisan key:generate
```

Lệnh này tạo khóa cho môi trường local của thành viên. Không gửi hoặc commit `.env` của máy cá nhân cho cả nhóm.

### 8.6. Tạo database và migrate

Tạo database `book_store_db` trong phpMyAdmin, rồi chạy:

```bat
C:\xampp\php_new\php.exe artisan config:clear
C:\xampp\php_new\php.exe artisan migrate
```

Chỉ chạy seeder nếu nhóm đã cung cấp và hướng dẫn sử dụng:

```bat
C:\xampp\php_new\php.exe artisan db:seed
```

Không chạy `migrate:fresh` nếu chưa chắc database có thể xóa.

### 8.7. Frontend (nếu có)

Nếu repository có `package.json` và dùng Vite:

1. Cài Node.js từ <https://nodejs.org/>.
2. Tại thư mục dự án, chạy:

```bat
npm install
npm run dev
```

Giữ terminal Vite mở khi phát triển. Nếu cần build tài nguyên tĩnh:

```bat
npm run build
```

Không cần cài Node.js nếu dự án không sử dụng frontend build.

### 8.8. Khởi chạy

```bat
C:\xampp\php_new\php.exe artisan serve
```

Mở URL được in trong terminal.

---

## 9. Chạy dự án hằng ngày

Khi đã cài đặt xong, thông thường bạn chỉ cần:

1. Mở XAMPP và Start MySQL.
2. Mở terminal tại dự án.
3. Chạy server Laravel.

```bat
cd /d C:\xampp\htdocs\book-management
C:\xampp\php_new\php.exe artisan serve
```

Nếu làm việc với nhánh Git, trước khi bắt đầu có thể cập nhật mã nguồn:

```bat
git status
git pull
```

Nếu `composer.lock` thay đổi sau khi pull:

```bat
C:\xampp\php_new\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install
```

Nếu có migration mới:

```bat
C:\xampp\php_new\php.exe artisan migrate
```

Nếu Composer PHAR không nằm ở đường dẫn ví dụ, hãy thay bằng đường dẫn Composer thực tế.

---

## 10. Lỗi thường gặp và cách xử lý

### Lỗi 1 — `php -v` vẫn ra PHP 8.2

**Nguyên nhân:** PATH đang trỏ tới `C:\xampp\php`.

**Cách xử lý:**

```bat
C:\xampp\php_new\php.exe -v
```

Luôn dùng đường dẫn PHP 8.4 đầy đủ hoặc điều chỉnh PATH rồi mở terminal mới. Kiểm tra thứ tự bằng `where php`.

### Lỗi 2 — Laravel yêu cầu PHP >= 8.3

**Nguyên nhân:** Composer đang chạy bằng PHP 8.2.

**Cách xử lý:** gọi Composer bằng PHP 8.4:

```bat
C:\xampp\php_new\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar --version
```

Sau đó dùng đúng executable đó để chạy lệnh Composer.

### Lỗi 3 — `The zip extension is missing`

**Kiểm tra:**

```bat
C:\xampp\php_new\php.exe --ini
C:\xampp\php_new\php.exe -m | findstr /I "zip"
```

**Xử lý:**

- Sửa đúng `php.ini` được in ra từ `--ini`.
- Bật `extension=zip`, không phải `extension=unzip`.
- Kiểm tra `extension_dir` và file DLL ZIP trong thư mục `ext`.
- Mở terminal mới và kiểm tra lại.

### Lỗi 4 — `could not find driver` / PDO MySQL

**Nguyên nhân:** PHP chưa bật `pdo_mysql` hoặc `.env` chưa đúng.

```bat
C:\xampp\php_new\php.exe -m | findstr /I "pdo_mysql mysqli"
```

Nếu thiếu, bật `extension=pdo_mysql` trong đúng `php.ini`. Kiểm tra database, username, password và port trong `.env`.

### Lỗi 5 — `Target directory is not empty`

`create-project ... .` yêu cầu thư mục đích phù hợp và thường phải trống.

- Nếu đang tạo dự án mới, dùng một thư mục trống.
- Nếu đã clone repository, **không chạy `create-project`**; chạy `composer install`.

### Lỗi 6 — `composer` không được nhận diện

- Mở terminal mới.
- Chạy `where composer`.
- Kiểm tra Composer đã được cài và PATH đã cập nhật.
- Nếu gọi `composer.phar`, xác minh đường dẫn file tồn tại.

### Lỗi 7 — `No application encryption key has been specified`

Kiểm tra `.env` có tồn tại. Nếu thiếu APP_KEY:

```bat
C:\xampp\php_new\php.exe artisan key:generate
```

### Lỗi 8 — `Unknown database 'book_store_db'`

Tạo database trong phpMyAdmin hoặc sửa `DB_DATABASE` cho khớp tên database hiện có.

### Lỗi 9 — Không kết nối được MySQL / `Connection refused`

- Start MySQL trong XAMPP.
- Kiểm tra `DB_HOST=127.0.0.1`.
- Kiểm tra `DB_PORT=3306` có đúng với máy không.
- Kiểm tra username/password và cổng không bị thay đổi.

### Lỗi 10 — `Table ... doesn't exist`

Ví dụ:

```text
SQLSTATE[42S02]: Base table or view not found
Table 'book_store_db.sessions' doesn't exist
```

Thực hiện lần lượt:

1. Xác nhận tên database trong `.env`.
2. Mở phpMyAdmin, chọn đúng database và kiểm tra bảng.
3. Kiểm tra cấu hình Laravel:

```bat
C:\xampp\php_new\php.exe artisan config:clear
C:\xampp\php_new\php.exe artisan config:show session
C:\xampp\php_new\php.exe artisan config:show database
C:\xampp\php_new\php.exe artisan migrate:status
```

4. Kiểm tra migration trong `database/migrations`.
5. Nếu migration tương ứng chưa có, mới tạo migration thích hợp và chạy `migrate`.

Không tạo migration trùng và không chạy `migrate:fresh` chỉ để thử sửa lỗi.

### Lỗi 11 — `Migration already exists`

Artisan báo migration đã tồn tại. Đừng chạy lệnh tạo lặp lại liên tục.

```bat
dir database\migrations
C:\xampp\php_new\php.exe artisan migrate:status
```

Tìm file migration liên quan, xem nội dung và kiểm tra bảng `migrations` trước khi quyết định bước tiếp theo. Nếu kết quả không nhất quán, thu thập thông báo lỗi đầy đủ thay vì xóa file theo phỏng đoán.

### Lỗi 12 — `Nothing to migrate`

Điều đó có nghĩa Laravel không thấy migration mới cần chạy theo trạng thái hiện tại. Nó **không chứng minh tất cả bảng trong database đều tồn tại**.

Kiểm tra:

```bat
C:\xampp\php_new\php.exe artisan migrate:status
```

So sánh danh sách migration với bảng trong phpMyAdmin.

### Lỗi 13 — `Vite manifest not found`

Nếu dự án dùng Vite, cài Node.js và chạy:

```bat
npm install
npm run build
```

Hoặc trong chế độ phát triển:

```bat
npm run dev
```

### Lỗi 14 — Apache lỗi sau khi thay PHP

Không thay toàn bộ `C:\xampp\php` bằng bản PHP tải riêng một cách tùy tiện. PHP CLI và PHP tích hợp Apache có thể được cấu hình khác nhau. Dùng `C:\xampp\php_new\php.exe artisan serve` nếu mục tiêu là chạy Laravel qua CLI.

### Lỗi 15 — Website vẫn lỗi 500 nhưng chưa rõ nguyên nhân

1. Đọc lỗi đầy đủ trên trình duyệt hoặc terminal.
2. Kiểm tra phần cuối `storage/logs/laravel.log`.
3. Kiểm tra `.env`, kết nối database và migration liên quan.
4. Sau khi sửa cấu hình, có thể chạy:

```bat
C:\xampp\php_new\php.exe artisan optimize:clear
```

Không chia sẻ file `.env` công khai vì có thể chứa thông tin nhạy cảm.

---

## 11. Nhật ký xử lý lỗi bảng `sessions`

Phần này ghi lại lỗi thực tế gặp trong quá trình thiết lập dự án.

### 11.1. Lỗi ban đầu

Laravel báo:

```text
SQLSTATE[42S02]: Base table or view not found: 1146
Table 'book_store_db.sessions' doesn't exist
```

`.env` có:

```dotenv
DB_CONNECTION=mysql
DB_DATABASE=book_store_db
SESSION_DRIVER=database
```

### 11.2. Các lệnh đã chạy

Kiểm tra file migration:

```bat
dir database\migrations
```

Danh sách ban đầu có ba migration mặc định:

```text
0001_01_01_000000_create_users_table.php
0001_01_01_000001_create_cache_table.php
0001_01_01_000002_create_jobs_table.php
```

Thử tạo migration session:

```bat
C:\xampp\php_new\php.exe artisan make:session-table
```

Kết quả:

```text
ERROR  Migration already exists.
```

Chạy migration:

```bat
C:\xampp\php_new\php.exe artisan migrate
```

Kết quả:

```text
INFO  Nothing to migrate.
```

Kiểm tra trạng thái:

```bat
C:\xampp\php_new\php.exe artisan migrate:status
```

Ba migration mặc định được ghi nhận ở trạng thái `Ran`.

### 11.3. Kiểm tra cấu hình

Đã chạy:

```bat
C:\xampp\php_new\php.exe artisan config:clear
C:\xampp\php_new\php.exe artisan config:show session
C:\xampp\php_new\php.exe artisan config:show database
```

Kết quả liên quan:

```text
session driver: database
session table: sessions
database default: mysql
database: book_store_db
host: 127.0.0.1
port: 3306
username: root
```

### 11.4. Kiểm tra trong phpMyAdmin và Tinker

Trong phpMyAdmin, bảng `sessions` tồn tại và có các cột:

```text
id
user_id
ip_address
user_agent
payload
last_activity
```

Mở Tinker:

```bat
C:\xampp\php_new\php.exe artisan tinker
```

Chạy:

```php
DB::connection()->getDatabaseName();
```

Kết quả:

```text
"book_store_db"
```

Chạy:

```php
DB::select('SHOW TABLES LIKE "sessions"');
```

Kết quả trả về bảng `sessions`.

Chạy:

```php
DB::table('sessions')->count();
```

Kết quả:

```text
0
```

Số `0` không phải lỗi; bảng chưa có bản ghi session. Các lệnh này xác nhận Laravel có thể nhìn thấy và truy vấn bảng `sessions` trong kết nối Tinker hiện tại.

Thoát Tinker:

```php
exit
```

Đã được hướng dẫn dọn cache:

```bat
C:\xampp\php_new\php.exe artisan optimize:clear
```

Khởi động server:

```bat
C:\xampp\php_new\php.exe artisan serve
```

Người dùng xác nhận website đã chạy được.

> **Kết luận chính xác:** Website đã chạy lại sau quá trình kiểm tra và dọn cache/khởi động lại. Dữ kiện thu thập được xác nhận database, bảng `sessions` và cấu hình Tinker truy cập được; không đủ bằng chứng để khẳng định một thao tác đơn lẻ là nguyên nhân trực tiếp sửa lỗi ban đầu.

---

## 12. Quy ước Git của nhóm

### Nên commit

- Mã nguồn dự án.
- `composer.json`, `composer.lock`.
- Migration, seeder, factory.
- `.env.example` với giá trị mẫu không có bí mật.
- `package.json` và lockfile npm nếu dự án dùng npm.
- Tài liệu hướng dẫn và README.

### Không nên commit

- `.env` chứa cấu hình cá nhân hoặc bí mật.
- `vendor/` (cài lại bằng Composer).
- `node_modules/` (cài lại bằng npm).
- Log và cache sinh tự động không cần thiết.
- Mật khẩu database, API token hoặc khóa bí mật.

Kiểm tra `.gitignore` của Laravel trước khi commit.

### Khi pull code mới

```bat
git status
git pull
```

Nếu `composer.lock` thay đổi:

```bat
C:\xampp\php_new\php.exe C:\ProgramData\ComposerSetup\bin\composer.phar install
```

Nếu có migration mới:

```bat
C:\xampp\php_new\php.exe artisan migrate
```

Không tự ý chạy `composer update` hoặc `migrate:fresh` khi chưa có lý do rõ ràng.

---

## 13. Checklist thành viên mới

- [ ] Cài Git for Windows.
- [ ] Cài XAMPP và xác nhận MySQL chạy được.
- [ ] Cài PHP 8.4.x.
- [ ] Kiểm tra `php.ini` và extension cần thiết.
- [ ] Cài Composer 2.x.
- [ ] Xác nhận Composer chạy bằng PHP 8.4.
- [ ] Clone đúng repository và branch.
- [ ] Chạy `composer install`.
- [ ] Tạo `.env` từ `.env.example`.
- [ ] Tạo database đúng tên trong `.env`.
- [ ] Chạy `php artisan key:generate` nếu cần.
- [ ] Chạy `php artisan migrate`.
- [ ] Cài Node.js và chạy npm nếu dự án dùng Vite.
- [ ] Chạy `php artisan serve`.
- [ ] Mở được website tại URL hiển thị trong terminal.
- [ ] Không commit `.env`, `vendor/` hoặc `node_modules/`.

---

## 14. Liên kết chính thức

- Laravel: <https://laravel.com/docs>
- Laravel installation: <https://laravel.com/docs/installation>
- Laravel configuration: <https://laravel.com/docs/configuration>
- Laravel migrations: <https://laravel.com/docs/migrations>
- Composer download: <https://getcomposer.org/download/>
- PHP for Windows: <https://windows.php.net/download/>
- XAMPP: <https://www.apachefriends.org/>
- Git for Windows: <https://git-scm.com/download/win>
- Node.js: <https://nodejs.org/>
