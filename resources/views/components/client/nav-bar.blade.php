@php
    $menuItems = $mainMenu ?? [];
    $brandTitle = data_get($webProfile, 'title', config('app.name'));
    $brandLogo = data_get($webProfile, 'logo_url');
    $hotline = data_get($webProfile, 'hotline');
    $authUser = $authUser ?? null;
    $isCustomer = $authUser && ($authUser->role ?? null) === 'customer';
    $customerLinks = $customerLinks ?? [];
    $currentLocale = app()->getLocale();
    $languageOptions = [
        ['code' => 'vi', 'label' => __('client.nav.languages.vi'), 'flag' => asset('/client/icons/vn-flag.svg')],
        ['code' => 'en', 'label' => __('client.nav.languages.en'), 'flag' => asset('/client/icons/en-flag.svg')],
    ];
    $currentLanguage = collect($languageOptions)->firstWhere('code', $currentLocale);
@endphp

<header class="bg-white/80 shadow-sm sticky top-0 z-50 backdrop-blur-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            {{-- Brand Logo --}}
            <a href="{{ route('client.home') }}" class="flex-shrink-0" aria-label="{{ __('client.nav.home_aria') }}">
                @if ($brandLogo)
                    <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10 w-auto">
                @else
                    <span class="text-2xl font-extrabold text-blue-600">{{ $brandTitle }}</span>
                @endif
            </a>

            {{-- Desktop Navigation --}}
            <nav class="hidden lg:flex items-center space-x-1">
                @foreach ($menuItems as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $isParentOfActive = $item->isParentOfActive ?? false;
                    @endphp
                    @if (empty($item->children))
                        <a href="{{ url($item->url) }}"
                           class="font-semibold transition-colors duration-200 px-4 py-2 rounded-lg {{ $isActive ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100 hover:text-blue-600' }}">
                            {{ $item->name }}
                        </a>
                    @else
                        <div class="relative group">
                            <div
                                class="flex items-center gap-1.5 cursor-pointer font-semibold transition-colors duration-200 px-4 py-2 rounded-lg {{ $isActive || $isParentOfActive ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100 hover:text-blue-600' }}">
                                <span>{{ $item->name }}</span>
                                <i class="fa-solid fa-chevron-down text-xs transition-transform duration-300 group-hover:rotate-180"></i>
                            </div>
                            <div
                                class="absolute left-0 mt-2 w-64 bg-white border border-slate-100 rounded-xl shadow-lg py-2 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                                @foreach ($item->children as $child)
                                    @php
                                        $isChildActive = $child->isActive ?? false;
                                        $isChildParentOfActive = $child->isParentOfActive ?? false;
                                    @endphp
                                    @if (empty($child->children))
                                        <a href="{{ url($child->url) }}"
                                           class="block px-4 py-2 text-sm rounded-md mx-1 {{ $isChildActive ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                                            {{ $child->name }}
                                        </a>
                                    @else
                                        <div class="relative group/submenu">
                                            <div
                                                class="flex justify-between items-center px-4 py-2 text-sm cursor-pointer rounded-md mx-1 {{ $isChildActive || $isChildParentOfActive ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}">
                                                <span>{{ $child->name }}</span>
                                                <i class="fa-solid fa-chevron-right text-xs"></i>
                                            </div>
                                            <div
                                                class="absolute left-full top-0 -mt-1 ml-1 w-64 bg-white border border-slate-100 rounded-xl shadow-lg py-2 opacity-0 invisible group-hover/submenu:opacity-100 group-hover/submenu:visible transition-all duration-300">
                                                @foreach ($child->children as $grandChild)
                                                    <a href="{{ url($grandChild->url) }}"
                                                       class="block px-4 py-2 text-sm rounded-md mx-1 {{ ($grandChild->isActive ?? false) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-500 hover:bg-blue-50 hover:text-blue-700' }}">
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

            {{-- Desktop Actions --}}
            <div class="hidden lg:flex items-center gap-4">
                {{-- Language Switcher --}}
                <div class="relative group">
                    <button class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors">
                        @if($currentLanguage && $currentLanguage['flag'])
                            <img src="{{ $currentLanguage['flag'] }}" alt="{{ $currentLanguage['label'] }}"
                                 class="w-5 h-5 rounded-full object-cover">
                        @endif
                        <span class="font-semibold text-sm text-slate-700">{{ $currentLanguage['code'] }}</span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-500"></i>
                    </button>
                    <div
                        class="absolute right-0 mt-2 w-40 bg-white border border-slate-100 rounded-xl shadow-lg py-1.5 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                        @foreach ($languageOptions as $language)
                            <a href="{{ route('client.locale.switch', ['locale' => $language['code']]) }}"
                               class="flex items-center gap-3 px-4 py-2 text-sm hover:bg-slate-100 {{ $currentLocale === $language['code'] ? 'font-bold text-blue-600' : 'text-slate-700' }}">
                                <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}"
                                     class="w-5 h-5 rounded-full object-cover">
                                <span>{{ $language['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Hotline --}}
                @if ($hotline)
                    <a href="tel:{{ str_replace([' ', '.'], '', $hotline) }}"
                       class="flex items-center gap-2 text-green-600 font-bold px-4 py-2 rounded-lg hover:bg-red-50 transition-colors duration-200">
                        <i class="fas fa-phone-alt text-lg"></i>
                        <span class="text-sm">{{ $hotline }}</span>
                    </a>
                @endif

                {{-- Auth Section --}}
                @if ($isCustomer)
                    <div class="relative group">
                        <button
                            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors">
                            <span
                                class="font-semibold text-sm text-slate-800">{{ \Illuminate\Support\Str::limit($authUser->name, 15) }}</span>
                            <i class="fa-solid fa-chevron-down text-xs text-slate-500"></i>
                        </button>
                        <div
                            class="absolute right-0 mt-2 w-64 bg-white border border-slate-100 rounded-xl shadow-lg py-2 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="font-semibold text-slate-900">{{ $authUser->name }}</p>
                                <p class="text-sm text-slate-500 truncate">{{ $authUser->email ?? $authUser->phone }}</p>
                            </div>
                            <div class="py-2">
                                @foreach ($customerLinks as $link)
                                    <a href="{{ $link['url'] }}"
                                       class="flex items-center gap-3 px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-blue-600">
                                        <i class="{{ $link['icon'] }} w-4 text-center text-slate-400"></i>
                                        <span>{{ $link['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                            <form method="POST" action="{{ route('client.logout') }}"
                                  class="pt-2 border-t border-slate-100">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                                    <span>{{ __('client.nav.logout') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('client.login') }}"
                           class="text-sm font-semibold text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-100 transition-colors">
                            {{ __('client.nav.login') }}
                        </a>
                        <a href="{{ route('client.register') }}"
                           class="text-sm font-semibold bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            {{ __('client.nav.register') }}
                        </a>
                    </div>
                @endif
            </div>

            {{-- Mobile Menu Button --}}
            <button id="mobile-menu-button" class="lg:hidden text-2xl text-slate-700"
                    aria-label="{{ __('client.nav.open_menu') }}">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div id="mobile-menu"
         class="lg:hidden fixed top-0 left-0 w-80 h-full bg-white shadow-lg z-[100] transform -translate-x-full"
         aria-hidden="true">
        <div class="p-5 flex flex-col h-full">
            {{-- Header --}}
            <div class="flex justify-between items-center mb-6">
                <a href="{{ route('client.home') }}" class="text-2xl font-extrabold text-blue-600"
                   aria-label="{{ __('client.nav.home_aria') }}">
                    @if ($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandTitle }}" class="h-10">
                    @else
                        {{ $brandTitle }}
                    @endif
                </a>
                <button id="close-mobile-menu" class="text-3xl text-slate-500">&times;</button>
            </div>

            {{-- Auth for Logged Out --}}
            @if (!$isCustomer)
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('client.login') }}"
                       class="flex-1 text-center border border-slate-200 rounded-xl py-2.5 font-semibold text-slate-700">
                        {{ __('client.nav.login') }}
                    </a>
                    <a href="{{ route('client.register') }}"
                       class="flex-1 text-center bg-blue-600 text-white font-semibold rounded-xl py-2.5">
                        {{ __('client.nav.register') }}
                    </a>
                </div>
            @endif

            {{-- Navigation --}}
            <nav class="flex-grow overflow-y-auto space-y-1">
                @foreach ($menuItems as $item)
                    @php
                        $isActive = $item->isActive ?? false;
                        $isParentOfActive = $item->isParentOfActive ?? false;
                    @endphp
                    @if (empty($item->children))
                        <a href="{{ url($item->url) }}"
                           class="block font-semibold py-3 px-3 rounded-lg transition-colors duration-200 text-base {{ $isActive ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-100 hover:text-blue-600' }}">
                            {{ $item->name }}
                        </a>
                    @else
                        <details class="group" @if($isParentOfActive) open @endif>
                            <summary
                                class="flex justify-between items-center font-semibold py-3 px-3 rounded-lg transition-colors duration-200 text-base cursor-pointer {{ $isActive || $isParentOfActive ? 'bg-slate-100 text-blue-600' : 'text-slate-700 hover:bg-slate-100 hover:text-blue-600' }}">
                                <span>{{ $item->name }}</span>
                                <i class="fa-solid fa-chevron-down text-sm transition-transform duration-300 group-open:rotate-180"></i>
                            </summary>
                            <div class="pl-5 mt-1 space-y-1 border-l-2 border-slate-200 ml-3">
                                @foreach ($item->children as $child)
                                    @php
                                        $isChildActive = $child->isActive ?? false;
                                    @endphp
                                    <a href="{{ url($child->url) }}"
                                       class="block font-medium py-2 px-3 rounded-lg transition-colors duration-200 {{ $isChildActive ? 'text-blue-600 font-semibold' : 'text-slate-600 hover:text-blue-600 hover:bg-slate-100' }}">
                                        {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        </details>
                    @endif
                @endforeach
            </nav>

            {{-- Footer section of mobile menu --}}
            <div class="mt-auto pt-4 border-t border-slate-200 space-y-4">
                {{-- Auth for Logged In --}}
                @if ($isCustomer)
                    <div class="border border-slate-100 rounded-2xl p-4 bg-slate-50 space-y-3">
                        <div>
                            <p class="font-semibold text-slate-900 truncate">{{ $authUser->name }}</p>
                            <p class="text-sm text-slate-500 truncate">{{ $authUser->email ?? $authUser->phone }}</p>
                        </div>
                        <div class="grid grid-cols-1 gap-2 pt-3 border-t border-slate-200">
                            @foreach ($customerLinks as $link)
                                <a href="{{ $link['url'] }}"
                                   class="flex items-center gap-3 text-sm text-blue-600 hover:text-blue-700">
                                    <i class="{{ $link['icon'] }} w-4 text-center"></i>
                                    <span>{{ $link['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                        <form method="POST" action="{{ route('client.logout') }}"
                              class="pt-3 border-t border-slate-200">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center justify-start gap-3 text-sm text-red-600">
                                <i class="fa-solid fa-arrow-right-from-bracket w-4 text-center"></i>
                                <span>{{ __('client.nav.logout') }}</span>
                            </button>
                        </form>
                    </div>
                @endif
                {{-- Language Switcher --}}
                <div class="grid grid-cols-2 gap-2">
                    @foreach ($languageOptions as $language)
                        <a href="{{ route('client.locale.switch', ['locale' => $language['code']]) }}"
                           class="flex items-center justify-center gap-2 py-2 rounded-xl border text-sm font-semibold transition-colors duration-200 {{ $currentLocale === $language['code'] ? 'bg-blue-600 text-white border-blue-600' : 'border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                            <img src="{{ $language['flag'] }}" alt="{{ $language['label'] }}"
                                 class="w-5 h-5 rounded-full object-cover">
                            <span>{{ $language['label'] }}</span>
                        </a>
                    @endforeach
                </div>
                {{-- Hotline --}}
                @if ($hotline)
                    <a href="tel:{{ str_replace([' ', '.'], '', $hotline) }}"
                       class="flex items-center gap-3 bg-red-50 text-red-600 font-semibold px-4 py-3 rounded-xl">
                        <i class="fas fa-phone-alt"></i>
                        <span class="flex flex-col text-left leading-tight">
                            <span class="text-xs uppercase tracking-wide">{{ __('client.nav.hotline') }}</span>
                            <span>{{ $hotline }}</span>
                        </span>
                    </a>
                @endif
            </div>
        </div>
    </div>
    <div id="mobile-menu-overlay" class="hidden lg:hidden fixed inset-0 bg-black/50 z-[99]"></div>
</header>
