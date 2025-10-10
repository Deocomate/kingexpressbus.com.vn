README - Hướng dẫn cho Lập trình viên
Chào mừng bạn đến với dự án King Express Bus! Tài liệu này cung cấp các thông tin cần thiết để giúp bạn cài đặt, tìm hiểu và bắt đầu đóng góp cho dự án một cách nhanh chóng.

Mục lục
Giới thiệu

Yêu cầu Hệ thống

Hướng dẫn Cài đặt

Tổng quan về Công nghệ

Cấu trúc Thư mục

Luồng hoạt động & Các khái niệm chính

Các Lệnh thường dùng

1. Giới thiệu
King Express Bus là một hệ thống website quản lý và đặt vé xe khách, bao gồm 3 phần chính:

Client: Giao diện cho khách hàng để tìm kiếm tuyến đường, xem thông tin nhà xe, đặt vé và quản lý tài khoản cá nhân.

Admin: Trang quản trị tổng thể, cho phép quản lý các nhà xe, tuyến đường, địa điểm, đặt vé và các cấu hình hệ thống.

Company: Trang quản trị dành riêng cho các nhà xe đối tác, giúp họ quản lý thông tin nhà xe, đội xe, tuyến đường, chuyến xe và các đặt vé của riêng mình.

2. Yêu cầu Hệ thống
Để chạy dự án này, bạn cần cài đặt các phần mềm sau trên máy của mình:

PHP: ^8.2

Composer: 2.x

Node.js: ^18.x hoặc ^20.x

NPM: ^9.x hoặc ^10.x

Cơ sở dữ liệu: MySQL (hoặc MariaDB)

Web Server: Nginx hoặc Apache

3. Hướng dẫn Cài đặt
Thực hiện các bước sau để cài đặt môi trường phát triển локально:

Clone a repository:

git clone <repository_url>
cd admin.kingexpressbus.com

Cài đặt các gói phụ thuộc của Composer:

composer install

Tạo file môi trường:
Sao chép file .env.example thành .env và cấu hình các biến môi trường, đặc biệt là thông tin kết nối cơ sở dữ liệu (DB_*).

cp .env.example .env

Tạo khóa ứng dụng:

php artisan key:generate

Cấu hình Database:

Tạo một cơ sở dữ liệu mới (ví dụ: kingbus_dev).

Cập nhật các biến DB_DATABASE, DB_USERNAME, DB_PASSWORD trong file .env.

Chạy Migrations và Seeders:
Lệnh này sẽ tạo các bảng trong cơ sở dữ liệu và điền dữ liệu mẫu (nếu có).

php artisan migrate --seed

Tạo liên kết symbolic cho storage:

php artisan storage:link

Cài đặt các gói phụ thuộc của NPM:

npm install

Chạy server phát triển:
Dự án đã định nghĩa một script dev trong composer.json để chạy đồng thời server PHP, queue listener và Vite.

composer run dev

Sau khi chạy lệnh, truy cập http://127.0.0.1:8000 (hoặc cổng mà php artisan serve chỉ định) trên trình duyệt.

4. Tổng quan về Công nghệ
Backend: Laravel 12, PHP 8.2

Frontend:

Admin/Company: Blade templates với AdminLTE 3.

Client: Blade templates với Tailwind CSS.

Database: MySQL/MariaDB.

Asset Bundling: Vite.

File Management: CKFinder, Dropzone.js.

Email: Resend.

Debugging: Laravel Debugbar, Laravel Pail.

5. Cấu trúc Thư mục
Dưới đây là mô tả về các thư mục quan trọng nhất trong dự án:

app/Http/Controllers/: Chứa toàn bộ logic xử lý request.

Admin/: Controllers cho trang quản trị Admin.

Company/: Controllers cho trang quản trị của Nhà xe.

Client/: Controllers cho trang của Khách hàng.

Auth/: Controllers xử lý đăng nhập/đăng xuất cho Admin và Company.

app/Http/Middleware/: Chứa các middleware.

Roles/: Các middleware phân quyền truy cập dựa trên vai trò (AdminAuthMiddleware, CompanyAuthMiddleware, CustomerAuthMiddleware).

app/Models/: Lưu ý: Dự án chủ yếu sử dụng Laravel Query Builder (DB::table(...)) thay vì Eloquent. Chỉ có model User được định nghĩa.

app/View/Components/: Chứa các Blade Components được sử dụng lại trong các view, được phân chia theo Admin, Client.

config/: Chứa các file cấu hình của Laravel. File ckfinder.php đáng chú ý vì nó cấu hình cho trình quản lý file.

database/migrations/: Định nghĩa schema của cơ sở dữ liệu. File 2025_09_22_152829_create_database_tables.php chứa hầu hết các bảng chính.

database/seeders/: Chứa các lớp để điền dữ liệu mẫu vào database.

lang/: Chứa các file ngôn ngữ (đa ngôn ngữ Anh/Việt cho trang Client).

resources/views/: Chứa các file Blade template.

admin/, company/, client/: Được tổ chức tương ứng với các vai trò người dùng.

components/: Các file view cho Blade Components.

layouts/: Các layout chính của trang.

routes/web.php: Nơi định nghĩa tất cả các routes của ứng dụng. Các routes được nhóm theo admin, company, và client với các middleware và tiền tố tương ứng.

6. Luồng hoạt động & Các khái niệm chính
Phân quyền (Authentication & Authorization)
Hệ thống có 3 vai trò chính: admin, company, customer.

Việc đăng nhập của Admin và Company được xử lý chung qua Auth/AuthenticateController và được bảo vệ bởi middleware trong app/Http/Middleware/Roles.

Việc đăng nhập/đăng ký của Customer (Client) được xử lý riêng trong Client/AuthController.

Các nhóm route trong routes/web.php được bảo vệ bởi các middleware tương ứng (AdminAuthMiddleware, CompanyAuthMiddleware, CustomerAuthMiddleware) để đảm bảo đúng vai trò mới được truy cập.

Tương tác Database (Query Builder)
Lưu ý quan trọng: Dự án gần như hoàn toàn sử dụng Laravel Query Builder (DB::table(...)) để thực hiện các thao tác CRUD với cơ sở dữ liệu.

Cách tiếp cận này được áp dụng nhất quán trong tất cả các Controllers (Admin, Company, Client).

Khi phát triển tính năng mới, hãy tuân thủ quy ước này. Chỉ sử dụng Eloquent Model khi thực sự cần thiết và đã có sự thống nhất. Model User là ngoại lệ chính, được dùng cho hệ thống xác thực của Laravel.

Frontend (AdminLTE, Tailwind, Vite)
Giao diện Admin và Company được xây dựng trên template AdminLTE 3. Các thành phần giao diện được đóng gói thành Blade Components trong resources/views/components/admin/.

Giao diện Client sử dụng Tailwind CSS để có giao diện hiện đại và đáp ứng.

Tất cả tài sản frontend (JS, CSS) được quản lý và biên dịch bởi Vite. Chạy composer run dev để bắt đầu quá trình theo dõi và biên dịch file.

Quản lý File (CKFinder)
Dự án tích hợp CKFinder để quản lý việc tải lên và duyệt file/hình ảnh.

Controller System/CkFinderController chứa logic tùy chỉnh để xử lý upload (ví dụ: tối ưu hóa ảnh) từ Dropzone.

Các routes cho CKFinder được định nghĩa ở cuối file routes/web.php.

Component vendors/dropzone-ckfinder-setup.blade.php chứa các hàm JavaScript helper để khởi tạo Dropzone và tích hợp với CKFinder.

7. Các Lệnh thường dùng
Chạy môi trường phát triển:

composer run dev

Chạy Migrations:

php artisan migrate

Tạo một Migration mới:

php artisan make:migration create_new_table

Xóa cache:

php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
