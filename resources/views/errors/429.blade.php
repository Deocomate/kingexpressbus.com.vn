@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>429 - Quá Nhiều Yêu Cầu | {{ $web_profile->title ?? config('app.name') }}</title>
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
<body class="bg-gradient-to-br from-rose-50 via-white to-pink-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated 429 Icon -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-rose-600 to-pink-600 animate-pulse">
                    429
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-gauge-high text-6xl text-rose-200 opacity-30"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Quá Nhiều Yêu Cầu
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Bạn đã gửi quá nhiều yêu cầu trong thời gian ngắn. Vui lòng chờ một chút trước khi thử lại.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-hourglass-half text-rose-600"></i>
                    <span class="text-gray-700">Vui lòng chờ</span>
                    <span id="countdown" class="font-bold text-2xl text-rose-600">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="location.reload()"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-600 to-pink-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-rose-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-rotate-right"></i>
                    Thử Lại
                </button>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-home"></i>
                    Về Trang Chủ
                </a>
            </div>

            <!-- Rate Limit Info -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="bg-rose-50 border border-rose-200 rounded-lg p-6 max-w-md mx-auto">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-shield-halved text-rose-600 text-xl mt-1"></i>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-rose-900 mb-2">Bảo vệ hệ thống</p>
                            <p class="text-sm text-rose-800 mb-3">
                                Chúng tôi giới hạn số lượng yêu cầu để:
                            </p>
                            <ul class="text-sm text-rose-800 space-y-1 list-disc list-inside">
                                <li>Đảm bảo hiệu suất tốt cho tất cả người dùng</li>
                                <li>Bảo vệ hệ thống khỏi spam và tấn công</li>
                                <li>Duy trì chất lượng dịch vụ ổn định</li>
                            </ul>
                            <p class="text-sm text-rose-800 mt-3">
                                Vui lòng chờ một chút rồi thử lại. Cảm ơn bạn đã thông cảm!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Countdown only (no auto-redirect for rate limiting)
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');

        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdown);
                // Enable retry button visually
                const retryButton = document.querySelector('button[onclick="location.reload()"]');
                if (retryButton) {
                    retryButton.classList.add('ring-4', 'ring-rose-300', 'ring-opacity-50');
                    countdownElement.parentElement.innerHTML = '<i class="fa-solid fa-check text-green-600"></i> <span class="text-gray-700">Bạn có thể thử lại ngay bây giờ</span>';
                }
            }
        }, 1000);
    </script>
</body>
</html>
