@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi Máy Chủ | {{ $web_profile->title ?? config('app.name') }}</title>
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
<body class="bg-gradient-to-br from-purple-50 via-white to-pink-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated 500 Icon -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-600 animate-pulse">
                    500
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fa-solid fa-server text-6xl text-purple-200 opacity-30"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Lỗi Máy Chủ Nội Bộ
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Rất tiếc, đã xảy ra lỗi trên máy chủ. Chúng tôi đang khắc phục sự cố này. Vui lòng thử lại sau.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-clock text-purple-600"></i>
                    <span class="text-gray-700">Tự động chuyển về trang chủ sau</span>
                    <span id="countdown" class="font-bold text-2xl text-purple-600">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-pink-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-home"></i>
                    Về Trang Chủ
                </a>
                <button onclick="location.reload()"
                        class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-rotate-right"></i>
                    Thử Lại
                </button>
            </div>

            <!-- Error Details (if in debug mode) -->
            @if(config('app.debug') && isset($exception))
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <details class="text-left bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <summary class="cursor-pointer font-semibold text-gray-700 hover:text-gray-900">
                            <i class="fa-solid fa-bug mr-2"></i>Chi tiết lỗi (Debug Mode)
                        </summary>
                        <div class="mt-4 space-y-2">
                            <p class="text-sm text-gray-600"><strong>Message:</strong> {{ $exception->getMessage() }}</p>
                            <p class="text-sm text-gray-600"><strong>File:</strong> {{ $exception->getFile() }}</p>
                            <p class="text-sm text-gray-600"><strong>Line:</strong> {{ $exception->getLine() }}</p>
                        </div>
                    </details>
                </div>
            @endif

            <!-- Help Section -->
            <div class="mt-8">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 max-w-md mx-auto">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-info text-blue-600 text-xl mt-1"></i>
                        <div class="text-left">
                            <p class="text-sm font-semibold text-blue-900 mb-2">Lỗi vẫn tiếp diễn?</p>
                            <p class="text-sm text-blue-800">
                                Vui lòng liên hệ với chúng tôi qua hotline hoặc email để được hỗ trợ kịp thời.
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
