@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>419 - Phiên Làm Việc Hết Hạn | {{ $web_profile->title ?? config('app.name') }}</title>
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
<body class="bg-gradient-to-br from-teal-50 via-white to-cyan-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated 419 Icon -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-cyan-600 animate-pulse">
                    419
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-clock-rotate-left text-6xl text-teal-200 opacity-30"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Phiên Làm Việc Hết Hạn
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Phiên làm việc của bạn đã hết hạn do không hoạt động trong thời gian dài. Vui lòng thử lại.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-clock text-teal-600"></i>
                    <span class="text-gray-700">Tự động chuyển về trang chủ sau</span>
                    <span id="countdown" class="font-bold text-2xl text-teal-600">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="window.history.back()"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-teal-600 to-cyan-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-teal-700 hover:to-cyan-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-arrow-left"></i>
                    Quay Lại & Thử Lại
                </button>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-home"></i>
                    Về Trang Chủ
                </a>
            </div>

            <!-- Help Section -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="bg-cyan-50 border border-cyan-200 rounded-lg p-6 max-w-md mx-auto">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-lightbulb text-cyan-600 text-xl mt-1"></i>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-cyan-900 mb-2">Tại sao lỗi này xảy ra?</p>
                            <p class="text-sm text-cyan-800 mb-3">
                                Lỗi này thường xảy ra khi:
                            </p>
                            <ul class="text-sm text-cyan-800 space-y-1 list-disc list-inside">
                                <li>Trang đã mở quá lâu mà không có hoạt động</li>
                                <li>Bạn đã đăng xuất ở tab khác</li>
                                <li>Cookie hoặc session đã hết hạn</li>
                            </ul>
                            <p class="text-sm text-cyan-800 mt-3">
                                Hãy quay lại trang trước và thử lại thao tác.
                            </p>
                        </div>
                    </div>
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
