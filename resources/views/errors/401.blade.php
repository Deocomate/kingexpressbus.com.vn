@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - Chưa Xác Thực | {{ $web_profile->title ?? config('app.name') }}</title>
    <link rel="icon" href="{{ $web_profile->favicon_url ?? '/favicon.ico' }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-blue-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated 401 Icon -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-600 animate-pulse">
                    401
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-user-lock text-6xl text-indigo-200 opacity-30"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Chưa Xác Thực
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Bạn cần đăng nhập để truy cập trang này. Vui lòng đăng nhập hoặc đăng ký tài khoản.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-clock text-indigo-600"></i>
                    <span class="text-gray-700">Tự động chuyển đến đăng nhập sau</span>
                    <span id="countdown" class="font-bold text-2xl text-indigo-600">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('client.login') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Đăng Nhập
                </a>
                <a href="{{ route('client.register') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-user-plus"></i>
                    Đăng Ký
                </a>
            </div>

            <!-- Or go home -->
            <div class="mt-6">
                <a href="{{ url('/') }}" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                    <i class="fa-solid fa-arrow-left mr-1"></i>Hoặc về trang chủ
                </a>
            </div>

            <!-- Benefits Section -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Lợi ích khi đăng ký tài khoản</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl mx-auto">
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-4 border border-indigo-100">
                        <i class="fa-solid fa-ticket text-2xl text-indigo-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">Đặt vé dễ dàng</p>
                        <p class="text-xs text-gray-600 mt-1">Quản lý vé của bạn</p>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-4 border border-indigo-100">
                        <i class="fa-solid fa-clock-rotate-left text-2xl text-indigo-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">Lịch sử đặt vé</p>
                        <p class="text-xs text-gray-600 mt-1">Xem lại các chuyến đi</p>
                    </div>
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-lg p-4 border border-indigo-100">
                        <i class="fa-solid fa-bell text-2xl text-indigo-600 mb-2"></i>
                        <p class="text-sm font-semibold text-gray-800">Nhận thông báo</p>
                        <p class="text-xs text-gray-600 mt-1">Cập nhật ưu đãi mới</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Countdown and auto-redirect to login
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');

        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = '{{ route('client.login') }}';
            }
        }, 1000);

        // Cancel countdown if user interacts
        document.addEventListener('click', () => {
            clearInterval(countdown);
        });
    </script>
</body>
</html>
