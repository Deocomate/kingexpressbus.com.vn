@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Bảo Trì Hệ Thống | {{ $web_profile->title ?? config('app.name') }}</title>
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
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-yellow-50 via-white to-amber-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Animated Icon -->
            <div class="mb-8 relative">
                <div class="text-9xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-yellow-600 to-amber-600">
                    503
                </div>
                <div class="absolute inset-0 flex items-center justify-center float-animation">
                    <i class="fa-solid fa-screwdriver-wrench text-6xl text-yellow-300 opacity-40"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Hệ Thống Đang Bảo Trì
            </h1>
            <p class="text-lg text-gray-600 mb-8 max-w-md mx-auto">
                Chúng tôi đang nâng cấp hệ thống để mang đến trải nghiệm tốt hơn. Vui lòng quay lại sau ít phút.
            </p>

            <!-- Countdown Timer -->
            <div class="mb-8">
                <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                    <i class="fa-solid fa-clock text-yellow-600"></i>
                    <span class="text-gray-700">Thử lại sau</span>
                    <span id="countdown" class="font-bold text-2xl text-yellow-600">10</span>
                    <span class="text-gray-700">giây</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="location.reload()"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-600 to-amber-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-yellow-700 hover:to-amber-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-rotate-right"></i>
                    Thử Lại Ngay
                </button>
                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 bg-white text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-all duration-300 shadow-md hover:shadow-lg border border-gray-200">
                    <i class="fa-solid fa-home"></i>
                    Về Trang Chủ
                </a>
            </div>

            <!-- Maintenance Info -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-lg p-6 max-w-md mx-auto">
                    <div class="space-y-4">
                        <div class="flex items-center justify-center gap-3">
                            <div class="h-3 w-3 bg-yellow-500 rounded-full animate-pulse"></div>
                            <p class="text-sm font-semibold text-gray-800">
                                Đang nâng cấp hệ thống
                            </p>
                        </div>
                        @if(isset($message))
                            <p class="text-sm text-gray-600">
                                {{ $message }}
                            </p>
                        @else
                            <p class="text-sm text-gray-600">
                                Thời gian bảo trì dự kiến: 15-30 phút
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="mt-6">
                    <p class="text-sm text-gray-500 mb-3">Cần hỗ trợ khẩn cấp?</p>
                    <div class="flex flex-wrap gap-4 justify-center text-sm">
                        @if($web_profile->phone ?? null)
                            <a href="tel:{{ $web_profile->phone }}" class="text-yellow-600 hover:text-yellow-800 hover:underline">
                                <i class="fa-solid fa-phone mr-1"></i>{{ $web_profile->phone }}
                            </a>
                        @endif
                        @if($web_profile->email ?? null)
                            <a href="mailto:{{ $web_profile->email }}" class="text-yellow-600 hover:text-yellow-800 hover:underline">
                                <i class="fa-solid fa-envelope mr-1"></i>{{ $web_profile->email }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Countdown and auto-reload
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');

        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;

            if (seconds <= 0) {
                clearInterval(countdown);
                location.reload();
            }
        }, 1000);

        // Cancel countdown if user clicks retry button
        document.querySelector('button[onclick="location.reload()"]')?.addEventListener('click', () => {
            clearInterval(countdown);
        });
    </script>
</body>
</html>
