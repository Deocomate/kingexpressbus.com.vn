@php
    $web_profile = DB::table('web_profiles')->where('is_default', true)->first();
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>503 - Đang Bảo Trì | {{ $web_profile->title ?? config('app.name') }}</title>
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
<body class="bg-gradient-to-br from-emerald-50 via-white to-teal-50">
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="max-w-2xl w-full text-center">
            <!-- Maintenance Icon -->
            <div class="mb-8 relative">
                <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full float-animation">
                    <i class="fa-solid fa-tools text-5xl text-emerald-600"></i>
                </div>
            </div>

            <!-- Message -->
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                Đang Bảo Trì
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-md mx-auto">
                Chúng tôi đang nâng cấp hệ thống
            </p>

            @if(isset($message))
                <div class="mb-8 max-w-lg mx-auto">
                    <div class="bg-white rounded-lg p-6 shadow-lg border border-emerald-100">
                        <p class="text-gray-700">{{ $message }}</p>
                    </div>
                </div>
            @else
                <p class="text-base text-gray-500 mb-8">
                    Trang web sẽ sớm trở lại. Cảm ơn sự kiên nhẫn của bạn!
                </p>
            @endif

            <!-- Progress Bar -->
            <div class="mb-8 max-w-md mx-auto">
                <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 animate-pulse" style="width: 60%"></div>
                </div>
            </div>

            <!-- Estimated Time -->
            @if(isset($retryAfter))
                <div class="mb-8">
                    <div class="inline-flex items-center gap-3 bg-white rounded-full px-6 py-3 shadow-lg">
                        <i class="fa-solid fa-clock text-emerald-600"></i>
                        <span class="text-gray-700">Thời gian dự kiến:</span>
                        <span class="font-bold text-emerald-600">{{ $retryAfter }}</span>
                    </div>
                </div>
            @endif

            <!-- Refresh Button -->
            <button onclick="location.reload()"
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-emerald-700 hover:to-teal-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <i class="fa-solid fa-rotate-right"></i>
                Kiểm Tra Lại
            </button>

            <!-- Contact Info -->
            <div class="mt-12 pt-8 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-3">Cần hỗ trợ khẩn cấp?</p>
                <div class="flex flex-wrap gap-4 justify-center text-sm">
                    @if($web_profile->phone ?? null)
                        <a href="tel:{{ $web_profile->phone }}" class="text-emerald-600 hover:text-emerald-800 hover:underline">
                            <i class="fa-solid fa-phone mr-1"></i>{{ $web_profile->phone }}
                        </a>
                    @endif
                    @if($web_profile->email ?? null)
                        <a href="mailto:{{ $web_profile->email }}" class="text-emerald-600 hover:text-emerald-800 hover:underline">
                            <i class="fa-solid fa-envelope mr-1"></i>{{ $web_profile->email }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
