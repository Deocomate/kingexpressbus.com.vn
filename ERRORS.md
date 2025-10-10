# Error Pages - Quick Reference

## ✅ Đã Tạo 10 Error Pages

| Mã Lỗi | File | Màu Sắc | Auto-Redirect | Mô Tả |
|--------|------|---------|---------------|-------|
| 401 | 401.blade.php | Indigo/Blue | → Login (10s) | Chưa xác thực |
| 403 | 403.blade.php | Red/Orange | → Home (10s) | Truy cập bị từ chối |
| 404 | 404.blade.php | Blue/Indigo | → Home (10s) | Không tìm thấy trang |
| 405 | 405.blade.php | Orange/Amber | → Home (10s) | Phương thức không được phép |
| 419 | 419.blade.php | Teal/Cyan | → Home (10s) | Phiên làm việc hết hạn |
| 429 | 429.blade.php | Rose/Pink | ⏸️ No redirect | Quá nhiều yêu cầu |
| 500 | 500.blade.php | Purple/Pink | → Home (10s) | Lỗi máy chủ nội bộ |
| 503 | 503.blade.php | Yellow/Amber | ↻ Reload (10s) | Dịch vụ không khả dụng |
| - | maintenance.blade.php | Emerald/Teal | ↻ Auto (30s) | Đang bảo trì |
| * | layout.blade.php | Gray/Slate | → Home (10s) | Lỗi không xác định |

## 🎨 Tính Năng

- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Tailwind CSS + Font Awesome icons
- ✅ Countdown timer (10s)
- ✅ Cancel countdown on click
- ✅ Gradient animations
- ✅ Web profile integration
- ✅ Debug info (when APP_DEBUG=true)
- ✅ SEO friendly

## 🧪 Testing

```bash
# Test 404
https://yourdomain.com/not-found

# Test 503
php artisan down

# Test 500 (add to routes/web.php temporarily)
Route::get('/test-500', fn() => throw new Exception('Test'));
```

## 🔧 Maintenance Mode

```bash
# Bật maintenance mode
php artisan down

# Bật với message
php artisan down --message="Đang nâng cấp hệ thống"

# Cho phép IP truy cập
php artisan down --secret="admin-access"
# URL: https://yourdomain.com/admin-access

# Tắt maintenance mode
php artisan up
```

## 📝 Customize

### Thay đổi thời gian countdown:
```javascript
let seconds = 10; // Change to your desired seconds
```

### Thay đổi redirect URL:
```javascript
window.location.href = '{{ url('/') }}'; // Change '/' to your URL
```

## ⚠️ Lưu Ý

1. Laravel tự động load error pages từ `resources/views/errors/`
2. Không cần cấu hình thêm
3. Đảm bảo có record trong `web_profiles` table với `is_default = true`
4. Tắt debug mode trên production: `APP_DEBUG=false`

## 🎯 Routes Được Sử Dụng

- `route('client.home')` - Trang chủ
- `route('client.login')` - Đăng nhập
- `route('client.register')` - Đăng ký
- `route('client.routes.search')` - Tìm tuyến đường
- `route('client.companies.index')` - Danh sách nhà xe
- `route('client.contact')` - Liên hệ

Đảm bảo tất cả routes này tồn tại trong `routes/web.php`!
