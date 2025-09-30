@php
    $webProfileData = $webProfile ?? null;
    if (!$webProfileData && \Illuminate\Support\Facades\Schema::hasTable('web_profiles')) {
        $webProfileData = \Illuminate\Support\Facades\DB::table('web_profiles')->where('is_default', true)->first();
    }

    $mainMenuData = $mainMenu ?? [];
    if (empty($mainMenuData) && \Illuminate\Support\Facades\Schema::hasTable('menus')) {
        $mainMenuData = \Illuminate\Support\Facades\DB::table('menus')
            ->orderBy('parent_id')
            ->orderBy('priority')
            ->get();
    }

    $pageTitle = $title ?? data_get($webProfileData, 'title', config('app.name'));
    $pageDescription = $description ?? data_get($webProfileData, 'description', '');
    $faviconUrl = $favicon ?? data_get($webProfileData, 'favicon_url', '/favicon.ico');
    $bodyClassName = trim($bodyClass ?? '') !== '' ? trim($bodyClass) : 'bg-slate-50';
    $currentUrl = url()->current();
    $shareImage = data_get($webProfileData, 'share_image_url') ?? '/userfiles/files/web information/logo.jpg';
    $authUser = auth()->user();
    $customerNavLinks = [];
    if ($authUser && ($authUser->role ?? null) === 'customer') {
        $customerNavLinks = [
            [
                'label' => 'Trang ca nhan',
                'url' => route('client.profile.index'),
                'icon' => 'fa-solid fa-user',
            ],
            [
                'label' => 'Dat ve cua toi',
                'url' => route('client.profile.index') . '#history',
                'icon' => 'fa-solid fa-ticket',
            ],
        ];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $currentUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $currentUrl }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $shareImage }}">
    <link rel="icon" href="{{ $faviconUrl }}">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        #mobile-menu {
            transition: transform 0.3s ease-in-out;
        }

        .social-float {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .social-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s;
        }

        .social-icon:hover {
            transform: scale(1.1);
        }

        .messenger {
            background: linear-gradient(45deg, #0099ff, #a033ff);
        }

        .zalo {
            background-color: #0068FF;
        }

        .hotline {
            background-color: #d9534f;
        }

        details[open] > summary i.fa-chevron-down {
            transform: rotate(180deg);
        }

        details > summary {
            list-style: none;
        }

        details > summary::-webkit-details-marker {
            display: none;
        }
    </style>

    @stack('styles')
</head>

<body class="{{ $bodyClassName }}">
    <x-client.nav-bar :web-profile="$webProfileData" :main-menu="$mainMenuData" :auth-user="$authUser" :customer-links="$customerNavLinks" />

    <main>
        {{ $slot }}
    </main>

    <x-client.footer :web-profile="$webProfileData" />

    @if ($webProfileData)
        <div class="social-float">
            @if (data_get($webProfileData, 'facebook_url'))
                <a href="https://m.me/{{ basename(parse_url(data_get($webProfileData, 'facebook_url'), PHP_URL_PATH) ?? '') }}"
                    target="_blank" class="social-icon messenger" aria-label="Messenger">
                    <i class="fab fa-facebook-messenger"></i>
                </a>
            @endif
            @if (data_get($webProfileData, 'zalo_url'))
                <a href="{{ data_get($webProfileData, 'zalo_url') }}" target="_blank" class="social-icon zalo"
                    aria-label="Zalo">
                    <span class="font-bold">Za</span>
                </a>
            @endif
            @if (data_get($webProfileData, 'hotline'))
                <a href="tel:{{ str_replace([' ', '.'], '', data_get($webProfileData, 'hotline')) }}"
                    class="social-icon hotline" aria-label="Hotline">
                    <i class="fas fa-phone-alt"></i>
                </a>
            @endif
            @if (data_get($webProfileData, 'whatsapp'))
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', data_get($webProfileData, 'whatsapp')) }}"
                    target="_blank" class="social-icon bg-green-500" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            @endif
        </div>
    @endif

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            const closeMobileMenuButton = document.getElementById('close-mobile-menu');

            function toggleMenu() {
                mobileMenu.classList.toggle('-translate-x-full');
                mobileMenuOverlay.classList.toggle('hidden');
            }

            if (mobileMenuButton) mobileMenuButton.addEventListener('click', toggleMenu);
            if (mobileMenuOverlay) mobileMenuOverlay.addEventListener('click', toggleMenu);
            if (closeMobileMenuButton) closeMobileMenuButton.addEventListener('click', toggleMenu);

            toastr.options = {
                closeButton: true,
                progressBar: true,
                positionClass: 'toast-top-right'
            };

            @if (session('success'))
                toastr.success('{{ addslashes(session('success')) }}');
            @endif
            @if (session('error'))
                toastr.error('{{ addslashes(session('error')) }}');
            @endif
            @if (session('warning'))
                toastr.warning('{{ addslashes(session('warning')) }}');
            @endif
            @if (session('info'))
                toastr.info('{{ addslashes(session('info')) }}');
            @endif
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error('{{ addslashes($error) }}');
                @endforeach
            @endif
        });
    </script>

    @stack('scripts')
</body>

</html>
