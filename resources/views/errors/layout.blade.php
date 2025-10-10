@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi Không Xác Định | {{ $web_profile->title ?? config('app.name') }}</title>
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
<body class="bg-gradient-to-br from-slate-50 via-white to-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Error Icon -->
            <div class="mb-8">
                <i class="fa-solid fa-triangle-exclamation text-8xl text-gray-400"></i>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Đã Xảy Ra Lỗi
            </h1>
            <p class="text-lg text-gray-600 mb-2">
                Xin lỗi, đã có lỗi xảy ra khi xử lý yêu cầu của bạn.
            </p>

            @if(isset($exception) && $exception)
                <div class="mb-8 max-w-md mx-auto">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800 font-mono">
                            {{ $exception->getMessage() ?: 'Unknown error' }}
                        </p>
                    </div>
                </div>
            @else
                <p class="text-base text-gray-500 mb-8">
                    Vui lòng thử lại hoặc liên hệ với chúng tôi nếu vấn đề vẫn tiếp diễn.
                </p>
            @endif

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-clock text-gray-600"></i>
                    <span class="text-gray-700">Tự động chuyển về trang chủ sau</span>
                    <span id="countdown" class="font-bold text-2xl text-gray-900">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-home"></i>
                    Về Trang Chủ
                </a>
                <button onclick="window.history.back()"
                        class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-arrow-left"></i>
                    Quay Lại
                </button>
            </div>

            <!-- Debug Info (only in debug mode) -->
            @if(config('app.debug') && isset($exception))
                <div class="mt-12 pt-8 border-t border-gray-200">
                    <details class="text-left bg-gray-50 rounded-lg p-6 border border-gray-200 max-w-2xl mx-auto">
                        <summary class="cursor-pointer font-semibold text-gray-700 hover:text-gray-900">
                            <i class="fa-solid fa-bug mr-2"></i>Chi tiết lỗi (Debug Mode)
                        </summary>
                        <div class="mt-4 space-y-3">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Error Message</p>
                                <p class="text-sm text-gray-700 font-mono bg-white p-2 rounded border">
                                    {{ $exception->getMessage() }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">File</p>
                                <p class="text-sm text-gray-700 font-mono bg-white p-2 rounded border break-all">
                                    {{ $exception->getFile() }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Line</p>
                                <p class="text-sm text-gray-700 font-mono bg-white p-2 rounded border">
                                    {{ $exception->getLine() }}
                                </p>
                            </div>
                            @if(method_exists($exception, 'getStatusCode'))
                                <div>
                                    <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Status Code</p>
                                    <p class="text-sm text-gray-700 font-mono bg-white p-2 rounded border">
                                        {{ $exception->getStatusCode() }}
                                    </p>
                                </div>
                            @endif
                        </div>
                    </details>
                </div>
            @endif

            <!-- Contact Info -->
            <div class="mt-8">
                <p class="text-sm text-gray-500 mb-3">Cần hỗ trợ?</p>
                <div class="flex flex-wrap gap-4 justify-center text-sm">
                    @if($web_profile->phone ?? null)
                        <a href="tel:{{ $web_profile->phone }}" class="text-gray-600 hover:text-gray-900 hover:underline">
                            <i class="fa-solid fa-phone mr-1"></i>{{ $web_profile->phone }}
                        </a>
                    @endif
                    @if($web_profile->email ?? null)
                        <a href="mailto:{{ $web_profile->email }}" class="text-gray-600 hover:text-gray-900 hover:underline">
                            <i class="fa-solid fa-envelope mr-1"></i>{{ $web_profile->email }}
                        </a>
                    @endif
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
