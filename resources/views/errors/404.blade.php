@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không Tìm Thấy Trang | {{ $web_profile->title ?? config('app.name') }}</title>
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
<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated 404 Icon -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600 animate-pulse">
                    404
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-route text-6xl text-blue-200 opacity-30"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Không Tìm Thấy Trang
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Xin lỗi, trang bạn đang tìm kiếm không tồn tại hoặc đã được di chuyển.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-clock text-blue-600"></i>
                    <span class="text-gray-700">Tự động chuyển về trang chủ sau</span>
                    <span id="countdown" class="font-bold text-2xl text-blue-600">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-home"></i>
                    Về Trang Chủ
                </a>
                <button onclick="window.history.back()"
                        class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-arrow-left"></i>
                    Quay Lại
                </button>
            </div>

            <!-- Helpful Links -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-4">Có thể bạn đang tìm kiếm:</p>
                <div class="flex flex-wrap gap-3 justify-center">
                    <a href="{{ route('client.home') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        Trang Chủ
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="{{ route('client.routes.search') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        Tìm Tuyến Đường
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="{{ route('client.companies.index') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        Nhà Xe
                    </a>
                    <span class="text-gray-300">•</span>
                    <a href="{{ route('client.contact') }}"
                       class="text-sm text-blue-600 hover:text-blue-800 hover:underline">
                        Liên Hệ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Countdown and auto-redirect
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');

        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = '{{ url('/') }}';
            }
        }, 1000);

        // Cancel countdown if user interacts
        document.addEventListener('click', () => {
            clearInterval(countdown);
        });
    </script>
</body>
</html>
