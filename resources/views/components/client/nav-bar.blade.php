@php
    $menuItems = is_array($mainMenu) ? $mainMenu : collect($mainMenu)->all();
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = data_get($webProfile, 'logo_url');
    $hotline = data_get($webProfile, 'hotline');
    $authUser = $authUser ?? null;
    $isCustomer = $authUser && ($authUser->role ?? null) === 'customer';
    $customerLinks = $customerLinks ?? [];
@endphp
<header class="bg-white shadow-sm sticky top-0 z-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <a href="{{ route('client.home') }}" class="flex-shrink-0" aria-label="Trang chu">
                @if ($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10 w-auto">
                @else
                    <span class="text-2xl font-extrabold text-blue-600">{{ $brandTitle }}</span>
                @endif
            </a>
            <nav class="hidden lg:flex items-center space-x-8">
                @foreach ($menuItems as $item)
                    <a href="{{ url($item->url) }}"
                       class="text-slate-600 hover:text-blue-600 font-semibold transition-colors duration-200 pb-1 border-b-2 border-transparent hover:border-blue-600">
                        {{ $item->name }}
                    </a>
                @endforeach
            </nav>
            <div class="hidden lg:flex items-center space-x-6">
                @if ($hotline)
                    <a href="tel:{{ str_replace([' ', '.'], '', $hotline) }}" class="flex items-center bg-red-50 text-red-600 font-bold px-4 py-2 rounded-lg hover:bg-red-100 transition-colors duration-200">
                        <i class="fas fa-phone-alt text-xl" aria-hidden="true"></i>
                        <span class="ml-2 text-sm leading-tight">
                            <span>Hotline</span>
                            <span>{{ $hotline }}</span>
                        </span>
                    </a>
                @endif
                @if ($isCustomer)
                    <details class="relative">
                        <summary class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-blue-600 font-semibold">
                            <i class="fas fa-user-circle text-2xl" aria-hidden="true"></i>
                            <span>{{ $authUser->name ?? 'Tai khoan' }}</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </summary>
                        <div class="absolute right-0 mt-3 w-56 bg-white border border-slate-100 rounded-xl shadow-lg py-2">
                            @foreach ($customerLinks as $link)
                                <a href="{{ $link['url'] }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700">
                                    <i class="{{ $link['icon'] }} text-blue-500"></i>
                                    <span>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                            <form method="POST" action="{{ route('client.logout') }}" class="mt-2 border-t border-slate-100">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    Dang xuat
                                </button>
                            </form>
                        </div>
                    </details>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('client.login') }}" class="text-slate-600 hover:text-blue-600 font-semibold">Đăng nhập</a>
                        <a href="{{ route('client.register') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg transition">
                            <i class="fa-solid fa-user-plus"></i>
                            Đăng ký
                        </a>
                    </div>
                @endif
            </div>
            <button id="mobile-menu-button" class="lg:hidden text-2xl text-slate-700" aria-label="Mo menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="lg:hidden fixed top-0 left-0 w-80 h-full bg-white shadow-lg z-[100] transform -translate-x-full" aria-hidden="true">
        <div class="p-5 space-y-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('client.home') }}" class="text-2xl font-extrabold text-blue-600" aria-label="Trang chu">
                    @if ($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10">
                    @else
                        {{ $brandTitle }}
                    @endif
                </a>
                <button id="close-mobile-menu" class="text-2xl text-slate-500" aria-label="Dong menu">&times;</button>
            </div>
            @if ($isCustomer)
                <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50 space-y-2">
                    <p class="font-semibold text-slate-900">{{ $authUser->name }}</p>
                    <p class="text-sm text-slate-500">{{ $authUser->email ?? $authUser->phone }}</p>
                    <div class="grid grid-cols-1 gap-2 pt-2 border-t border-slate-100">
                        @foreach ($customerLinks as $link)
                            <a href="{{ $link['url'] }}" class="flex items-center gap-2 text-sm text-blue-600">
                                <i class="{{ $link['icon'] }}"></i>
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                    <form method="POST" action="{{ route('client.logout') }}" class="pt-2 border-t border-slate-100">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 text-sm text-red-600">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Dang xuat
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.login') }}" class="flex-1 text-center border border-slate-200 rounded-xl py-2 font-semibold text-slate-600">Đăng nhập</a>
                    <a href="{{ route('client.register') }}" class="flex-1 text-center bg-blue-600 text-white font-semibold rounded-xl py-2">Đăng ký</a>
                </div>
            @endif
            <nav class="flex flex-col space-y-2">
                @foreach ($menuItems as $item)
                    <a href="{{ url($item->url) }}"
                       class="text-slate-700 hover:text-blue-600 font-semibold py-3 px-3 rounded-lg hover:bg-slate-100 transition-colors duration-200 text-lg">
                        {{ $item->name }}
                    </a>
                @endforeach
            </nav>
            @if ($hotline)
                <a href="tel:{{ str_replace([' ', '.'], '', $hotline) }}" class="flex items-center gap-3 bg-red-50 text-red-600 font-semibold px-4 py-3 rounded-xl">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ $hotline }}</span>
                </a>
            @endif
        </div>
    </div>
    <div id="mobile-menu-overlay" class="hidden lg:hidden fixed inset-0 bg-black opacity-50 z-[99]"></div>
</header>
