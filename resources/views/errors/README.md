# Custom Error Pages - King Express Bus

## Tổng quan
Dự án đã được cấu hình với các trang lỗi tùy chỉnh đẹp mắt, phù hợp với thiết kế giao diện client sử dụng Tailwind CSS.

## Danh sách Error Pages

### 1. **401.blade.php** - Chưa Xác Thực (Unauthorized)
- **Khi nào xuất hiện**: Khi người dùng chưa đăng nhập nhưng cố truy cập trang yêu cầu xác thực
- **Hành động**: Tự động chuyển đến trang đăng nhập sau 10 giây
- **Màu chủ đạo**: Indigo/Blue
- **Icon**: `fa-user-lock`

### 2. **403.blade.php** - Truy Cập Bị Từ Chối (Forbidden)
- **Khi nào xuất hiện**: Người dùng không có quyền truy cập tài nguyên
- **Hành động**: Tự động chuyển về trang chủ sau 10 giây
- **Màu chủ đạo**: Red/Orange
- **Icon**: `fa-lock`

### 3. **404.blade.php** - Không Tìm Thấy Trang (Not Found)
- **Khi nào xuất hiện**: URL không tồn tại
- **Hành động**: Tự động chuyển về trang chủ sau 10 giây
- **Màu chủ đạo**: Blue/Indigo
- **Icon**: `fa-route`
- **Tính năng đặc biệt**: Hiển thị các liên kết hữu ích (Trang chủ, Tuyến đường, Nhà xe, Liên hệ)

### 4. **405.blade.php** - Phương Thức Không Được Phép (Method Not Allowed)
- **Khi nào xuất hiện**: Sử dụng sai HTTP method (VD: POST thay vì GET)
- **Hành động**: Tự động chuyển về trang chủ sau 10 giây
- **Màu chủ đạo**: Orange/Amber
- **Icon**: `fa-ban`

### 5. **419.blade.php** - Phiên Làm Việc Hết Hạn (CSRF Token Mismatch)
- **Khi nào xuất hiện**: Token CSRF hết hạn hoặc không hợp lệ
- **Hành động**: Tự động chuyển về trang chủ sau 10 giây
- **Màu chủ đạo**: Teal/Cyan
- **Icon**: `fa-clock-rotate-left`
- **Tính năng đặc biệt**: Giải thích tại sao lỗi xảy ra

### 6. **429.blade.php** - Quá Nhiều Yêu Cầu (Too Many Requests)
- **Khi nào xuất hiện**: Người dùng gửi quá nhiều request (rate limiting)
- **Hành động**: Chỉ đếm ngược, KHÔNG tự động redirect (để người dùng tự thử lại)
- **Màu chủ đạo**: Rose/Pink
- **Icon**: `fa-gauge-high`
- **Tính năng đặc biệt**: Giải thích về rate limiting

### 7. **500.blade.php** - Lỗi Máy Chủ Nội Bộ (Internal Server Error)
- **Khi nào xuất hiện**: Lỗi server không mong đợi
- **Hành động**: Tự động chuyển về trang chủ sau 10 giây
- **Màu chủ đạo**: Purple/Pink
- **Icon**: `fa-server`
- **Tính năng đặc biệt**: Hiển thị chi tiết lỗi khi `APP_DEBUG=true`

### 8. **503.blade.php** - Dịch Vụ Không Khả Dụng (Service Unavailable)
- **Khi nào xuất hiện**: Server quá tải hoặc đang bảo trì
- **Hành động**: Tự động reload trang sau 10 giây
- **Màu chủ đạo**: Yellow/Amber
- **Icon**: `fa-screwdriver-wrench`
- **Tính năng đặc biệt**: Animation float cho icon, progress bar

### 9. **maintenance.blade.php** - Trang Bảo Trì
- **Khi nào xuất hiện**: Khi bật maintenance mode (`php artisan down`)
- **Hành động**: Tự động reload mỗi 30 giây
- **Màu chủ đạo**: Emerald/Teal
- **Icon**: `fa-tools`
- **Tính năng đặc biệt**: Progress bar, float animation, hỗ trợ message tùy chỉnh

### 10. **layout.blade.php** - Template Mặc Định
- **Khi nào xuất hiện**: Các lỗi không có template riêng
- **Hành động**: Tự động chuyển về trang chủ sau 10 giây
- **Màu chủ đạo**: Gray/Slate
- **Icon**: `fa-triangle-exclamation`
- **Tính năng đặc biệt**: Hiển thị debug info khi `APP_DEBUG=true`

## Tính Năng Chung

### 1. Auto-Redirect (10 giây)
Tất cả các trang lỗi đều có countdown timer và tự động chuyển hướng sau 10 giây:
- **404, 403, 401, 405, 419, 500, layout**: Redirect về trang chủ `/`
- **401**: Redirect đến trang đăng nhập
- **429**: Chỉ đếm ngược, không tự động redirect
- **503**: Tự động reload trang hiện tại
- **maintenance**: Tự động reload mỗi 30 giây

### 2. Cancel Countdown
Người dùng có thể hủy countdown bằng cách click vào bất kỳ đâu trên trang.

### 3. Responsive Design
- Tối ưu cho mobile, tablet, desktop
- Sử dụng Tailwind CSS
- Font: Inter (Google Fonts)
- Icons: Font Awesome 6.5.1

### 4. Gradient & Animation
- Mỗi error page có bộ màu gradient riêng
- Số lỗi có hiệu ứng pulse animation
- Buttons có hover effects và transform

### 5. Web Profile Integration
Tất cả các trang đều tự động lấy thông tin từ bảng `web_profiles`:
- Favicon
- Site title
- Phone/Email (hiển thị ở footer)

### 6. Helpful Actions
Mỗi trang đều có ít nhất 2 nút hành động:
- Về trang chủ
- Quay lại / Thử lại / Đăng nhập (tùy context)

## Cách Laravel Sử Dụng

Laravel tự động load các view từ thư mục `resources/views/errors/` theo mã lỗi HTTP:
- `404.blade.php` cho HTTP 404
- `500.blade.php` cho HTTP 500
- v.v...

**Không cần cấu hình thêm gì!** Laravel sẽ tự động sử dụng các template này.

## Maintenance Mode

### Bật maintenance mode:
```bash
php artisan down
```

### Bật với message tùy chỉnh:
```bash
php artisan down --message="Đang nâng cấp hệ thống. Vui lòng quay lại sau 30 phút."
```

### Bật với retry time:
```bash
php artisan down --retry=60
```

### Cho phép IP cụ thể truy cập:
```bash
php artisan down --secret="king-express-2024"
# Truy cập: https://yourdomain.com/king-express-2024
```

### Tắt maintenance mode:
```bash
php artisan up
```

## Debug Mode

Khi `APP_DEBUG=true` trong `.env`, các trang lỗi sẽ hiển thị thêm thông tin debug:
- Error message
- File path
- Line number
- Stack trace (một số trang)

**Lưu ý**: Luôn tắt debug mode (`APP_DEBUG=false`) trên production!

## Tùy Chỉnh

### Thay đổi thời gian countdown:
Tìm và sửa dòng này trong mỗi file:
```javascript
let seconds = 10; // Thay 10 thành số giây bạn muốn
```

### Thay đổi hành vi redirect:
Sửa URL trong dòng:
```javascript
window.location.href = '{{ url('/') }}'; // Thay '/' thành URL bạn muốn
```

### Thay đổi màu sắc:
Sửa các class Tailwind trong HTML:
```html
<!-- Thay đổi từ blue sang green -->
<div class="text-blue-600"> → <div class="text-green-600">
<div class="from-blue-600 to-indigo-600"> → <div class="from-green-600 to-emerald-600">
```

## Testing

### Test 404 Error:
```
https://yourdomain.com/trang-khong-ton-tai
```

### Test 500 Error (trong debug mode):
Tạm thời thêm lỗi vào route:
```php
Route::get('/test-500', function() {
    throw new \Exception('Test error 500');
});
```

### Test 503 Error:
```bash
php artisan down
```

### Test 419 Error:
Submit form sau khi để page mở quá lâu (CSRF token expire)

### Test 429 Error:
Gửi nhiều requests liên tục đến một endpoint có rate limiting

## Browser Support
- Chrome/Edge: ✅ Full support
- Firefox: ✅ Full support
- Safari: ✅ Full support
- IE11: ⚠️ Partial support (một số CSS hiện đại có thể không hoạt động)

## Performance
- Tất cả assets load từ CDN (Tailwind, Font Awesome, Google Fonts)
- Không có dependencies local
- Fast load time (~1-2 seconds)

## Accessibility
- Semantic HTML
- ARIA labels cho icons
- Keyboard navigation friendly
- Screen reader compatible

## Lưu Ý Quan Trọng

1. **Web Profile Required**: Các trang lỗi sử dụng `web_profiles` table. Đảm bảo có ít nhất 1 record với `is_default = true`.

2. **Route Names**: Error 401 sử dụng `route('client.auth.login')` và `route('client.auth.register')`. Đảm bảo các route này tồn tại.

3. **Production**: Luôn test các error pages trên staging trước khi deploy lên production.

4. **Caching**: Nếu sửa error pages mà không thấy thay đổi, clear cache:
```bash
php artisan view:clear
php artisan cache:clear
```

## Credits
- Design: Custom cho King Express Bus
- CSS Framework: Tailwind CSS
- Icons: Font Awesome
- Fonts: Inter (Google Fonts)
