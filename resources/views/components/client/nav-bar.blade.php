@php
    $menuItems = $mainMenu ?? [];
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
            <a href="{{ route('client.home') }}" class="flex-shrink-0" aria-label="Trang chủ">
                @if ($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10 w-auto">
                @else
                    <span class="text-2xl font-extrabold text-blue-600">{{ $brandTitle }}</span>
                @endif
            </a>
            <nav class="hidden lg:flex items-center space-x-1">
                @foreach ($menuItems as $item)
                    @if (empty($item->children))
                        <a href="{{ url($item->url) }}"
                           class="text-slate-600 hover:text-blue-600 font-semibold transition-colors duration-200 px-4 py-2 rounded-lg hover:bg-slate-100">
                            {{ $item->name }}
                        </a>
                    @else
                        <div class="relative group">
                            <div
                                class="flex items-center gap-1 cursor-pointer text-slate-600 hover:text-blue-600 font-semibold transition-colors duration-200 px-4 py-2 rounded-lg hover:bg-slate-100">
                                <a href="{{ url($item->url) }}" class="flex-grow">{{ $item->name }}</a>
                                <i
                                    class="fa-solid fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </div>
                            <div
                                class="absolute left-0 mt-2 w-64 bg-white border border-slate-100 rounded-xl shadow-lg py-2 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                                @foreach ($item->children as $child)
                                    @if (empty($child->children))
                                        <a href="{{ url($child->url) }}"
                                           class="block px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700">
                                            {{ $child->name }}
                                        </a>
                                    @else
                                        <div class="relative group/submenu">
                                            <div
                                                class="flex justify-between items-center px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700 cursor-pointer">
                                                <a href="{{ url($child->url) }}"
                                                   class="flex-grow">{{ $child->name }}</a>
                                                <i class="fa-solid fa-chevron-right text-xs"></i>
                                            </div>
                                            <div
                                                class="absolute left-full top-0 mt-[-8px] ml-1 w-64 bg-white border border-slate-100 rounded-xl shadow-lg py-2 opacity-0 invisible group-hover/submenu:opacity-100 group-hover/submenu:visible transition-all duration-300">
                                                @foreach ($child->children as $grandChild)
                                                    <a href="{{ url($grandChild->url) }}"
                                                       class="block px-4 py-2 text-sm text-slate-500 hover:bg-blue-50 hover:text-blue-700">
                                                        {{ $grandChild->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </nav>
            <div class="hidden lg:flex items-center space-x-6">
                @if ($hotline)
                    <a href="tel:{{ str_replace([' ', '.'], '', $hotline) }}"
                       class="flex items-center bg-red-50 text-red-600 font-bold px-4 py-2 rounded-lg hover:bg-red-100 transition-colors duration-200">
                        <i class="fas fa-phone-alt text-xl" aria-hidden="true"></i>
                        <span class="ml-2 text-sm leading-tight">
                            <span>Hotline</span>
                            <span>{{ $hotline }}</span>
                        </span>
                    </a>
                @endif
                @if ($isCustomer)
                    <div class="relative group">
                        <div
                            class="flex items-center gap-2 cursor-pointer text-slate-600 hover:text-blue-600 font-semibold">
                            <i class="fas fa-user-circle text-2xl" aria-hidden="true"></i>
                            <span>{{ $authUser->name ?? 'Tài khoản' }}</span>
                            <i
                                class="fas fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                        </div>
                        <div
                            class="absolute right-0 mt-3 w-56 bg-white border border-slate-100 rounded-xl shadow-lg py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-4 group-hover:translate-y-0">
                            @foreach ($customerLinks as $link)
                                <a href="{{ $link['url'] }}"
                                   class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-blue-50 hover:text-blue-700">
                                    <i class="{{ $link['icon'] }} text-blue-500"></i>
                                    <span>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                            <form method="POST" action="{{ route('client.logout') }}"
                                  class="mt-2 border-t border-slate-100">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <a href="{{ route('client.login') }}"
                           class="text-slate-600 hover:text-blue-600 font-semibold">Đăng nhập</a>
                        <a href="{{ route('client.register') }}"
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg transition">
                            <i class="fa-solid fa-user-plus"></i>
                            Đăng ký
                        </a>
                    </div>
                @endif
            </div>
            <button id="mobile-menu-button" class="lg:hidden text-2xl text-slate-700" aria-label="Mở menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    <div id="mobile-menu"
         class="lg:hidden fixed top-0 left-0 w-80 h-full bg-white shadow-lg z-[100] transform -translate-x-full"
         aria-hidden="true">
        <div class="p-5 space-y-6">
            <div class="flex justify-between items-center">
                <a href="{{ route('client.home') }}" class="text-2xl font-extrabold text-blue-600"
                   aria-label="Trang chủ">
                    @if ($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10">
                    @else
                        {{ $brandTitle }}
                    @endif
                </a>
                <button id="close-mobile-menu" class="text-2xl text-slate-500" aria-label="Đóng menu">&times;</button>
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
                    <form method="POST" action="{{ route('client.logout') }}"
                          class="pt-2 border-t border-slate-100">
                        @csrf
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 text-sm text-red-600">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            @else
                <div class="flex items-center gap-3">
                    <a href="{{ route('client.login') }}"
                       class="flex-1 text-center border border-slate-200 rounded-xl py-2 font-semibold text-slate-600">Đăng
                        nhập</a>
                    <a href="{{ route('client.register') }}"
                       class="flex-1 text-center bg-blue-600 text-white font-semibold rounded-xl py-2">Đăng ký</a>
                </div>
            @endif
            <nav class="flex flex-col space-y-1">
                @foreach ($menuItems as $item)
                    @if (empty($item->children))
                        <a href="{{ url($item->url) }}"
                           class="text-slate-700 hover:text-blue-600 font-semibold py-3 px-3 rounded-lg hover:bg-slate-100 transition-colors duration-200 text-lg">
                            {{ $item->name }}
                        </a>
                    @else
                        <details class="group">
                            <summary
                                class="flex justify-between items-center text-slate-700 hover:text-blue-600 font-semibold py-3 px-3 rounded-lg hover:bg-slate-100 transition-colors duration-200 text-lg cursor-pointer">
                                <a href="{{ url($item->url) }}" class="flex-grow">{{ $item->name }}</a>
                                <i
                                    class="fa-solid fa-chevron-down text-sm transition-transform duration-300 group-open:rotate-180"></i>
                            </summary>
                            <div class="pl-4 mt-1 space-y-1 border-l-2 border-slate-200 ml-3">
                                @foreach ($item->children as $child)
                                    @if (empty($child->children))
                                        <a href="{{ url($child->url) }}"
                                           class="block text-slate-600 hover:text-blue-600 font-medium py-2 px-3 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                                            {{ $child->name }}
                                        </a>
                                    @else
                                        <details class="group/submenu">
                                            <summary
                                                class="flex justify-between items-center text-slate-600 hover:text-blue-600 font-medium py-2 px-3 rounded-lg hover:bg-slate-100 transition-colors duration-200 cursor-pointer">
                                                <a href="{{ url($child->url) }}"
                                                   class="flex-grow">{{ $child->name }}</a>
                                                <i
                                                    class="fa-solid fa-chevron-down text-xs transition-transform duration-300 group-open/submenu:rotate-180"></i>
                                            </summary>
                                            <div
                                                class="pl-4 mt-1 space-y-1 border-l-2 border-slate-200 ml-3">
                                                @foreach ($child->children as $grandChild)
                                                    <a href="{{ url($grandChild->url) }}"
                                                       class="block text-slate-500 hover:text-blue-600 font-normal py-2 px-3 rounded-lg hover:bg-slate-100 transition-colors duration-200">
                                                        {{ $grandChild->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        </details>
                                    @endif
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
            </nav>
            @if ($hotline)
                <a href="tel:{{ str_replace([' ', '.'], '', $hotline) }}"
                   class="flex items-center gap-3 bg-red-50 text-red-600 font-semibold px-4 py-3 rounded-xl">
                    <i class="fas fa-phone-alt"></i>
                    <span>{{ $hotline }}</span>
                </a>
            @endif
        </div>
    </div>
    <div id="mobile-menu-overlay" class="hidden lg:hidden fixed inset-0 bg-black opacity-50 z-[99]"></div>
</header>
