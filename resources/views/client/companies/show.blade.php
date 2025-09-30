<x-client.layout :title="$title ?? 'Thông tin nhà xe'" :description="$description ?? ''">
    @php
        $companyImage = $company->thumbnail_url ?? '/userfiles/files/web information/logo.jpg';
        $routes = $routes ?? collect();
        $busFleet = $busFleet ?? collect();
        $upcomingTrips = $upcomingTrips ?? collect();
        $statistics = $statistics ?? [];
    @endphp

    <section class="relative overflow-hidden bg-slate-900 text-white">
        <div class="absolute inset-0">
            <img src="/userfiles/files/kingexpressbus/sleeper/7.jpg" alt="{{ $company->name }}" class="h-full w-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-slate-900/80"></div>
        </div>
        <div class="relative container mx-auto px-4 py-16 space-y-10">
            <div class="flex flex-col lg:flex-row lg:items-center gap-8">
                <div class="w-20 h-20 rounded-3xl bg-white overflow-hidden flex-shrink-0 shadow-xl">
                    <img src="{{ $companyImage }}" alt="{{ $company->name }}" class="h-full w-full object-cover" loading="lazy">
                </div>
                <div class="space-y-4 flex-1">
                    <span class="inline-flex items-center gap-2 text-sm uppercase tracking-widest text-yellow-300">
                        <i class="fa-solid fa-building"></i>
                        Nhà xe đối tác
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">{{ $company->name }}</h1>
                    <p class="text-white/80 max-w-2xl">{{ $company->description ?? 'Nhà xe đồng hành cùng King Express Bus mang đến trải nghiệm tiện nghi, an toàn và đáng giá.' }}</p>
                    <div class="flex flex-wrap gap-4 text-sm text-white/70">
                        @if ($company->address)
                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-map-marker-alt"></i>{{ $company->address }}</span>
                        @endif
                        @if ($company->hotline)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->hotline) }}" class="inline-flex items-center gap-2 text-yellow-300 hover:text-yellow-200">
                                <i class="fa-solid fa-phone"></i>{{ $company->hotline }}
                            </a>
                        @endif
                        @if ($company->email)
                            <a href="mailto:{{ $company->email }}" class="inline-flex items-center gap-2 text-yellow-300 hover:text-yellow-200">
                                <i class="fa-solid fa-envelope"></i>{{ $company->email }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ $statistics['route_count'] ?? $routes->count() }}</p>
                    <p class="text-sm text-white/70">Tuyến đang khai thác</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ $statistics['fleet_size'] ?? $busFleet->count() }}</p>
                    <p class="text-sm text-white/70">Số lượng xe</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ $statistics['active_trip_count'] ?? $upcomingTrips->count() }}</p>
                    <p class="text-sm text-white/70">Chuyến sắp chạy</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ isset($statistics['min_price']) && $statistics['min_price'] ? number_format($statistics['min_price']) . ' đ' : 'Liên hệ' }}</p>
                    <p class="text-sm text-white/70">Giá vé từ</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dedicated route search section under company header -->
    <section id="search-section" class="py-8 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="bg-white border border-slate-100 rounded-3xl p-4 shadow-sm">
                <x-client.search-bar :search-data="$searchData" submit-label="Tìm tuyến" />
            </div>
        </div>
    </section>

    <section class="py-12 bg-slate-50">
        <div class="container mx-auto px-4 grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-8">
                @if ($routes->isNotEmpty())
                    <section class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-slate-900">Tuyến đang khai thác</h2>
                            <span class="text-sm text-slate-500">{{ $routes->count() }} tuyến</span>
                        </div>
                        <div class="space-y-5">
                            @foreach ($routes as $route)
                                <article class="border border-slate-100 rounded-2xl p-5 hover:border-blue-200 hover:bg-blue-50 transition">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div>
                                            <p class="text-lg font-semibold text-slate-900">{{ $route->name }}</p>
                                            <p class="text-sm text-slate-500">{{ $route->description ?? 'Thông tin tuyến sẽ được cập nhật.' }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
                                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-clock"></i>{{ $route->duration ?? 'Đang cập nhật' }}</span>
                                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-road"></i>{{ $route->distance_km ? $route->distance_km . ' km' : 'Khoảng cách đang cập nhật' }}</span>
                                            <span class="inline-flex items-center gap-2 text-blue-600 font-semibold">
                                                <i class="fa-solid fa-ticket"></i>
                                                {{ $route->min_price ? 'Giá từ ' . number_format($route->min_price) . ' đ' : 'Liên hệ giá' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                                        <a href="{{ route('client.routes.show', ['slug' => $route->route_slug]) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold">
                                            Xem tuyến tổng quan
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                        <a href="{{ route('client.booking.create', ['company_route' => $route->slug]) }}" class="inline-flex items-center gap-2 text-emerald-600 hover:text-emerald-700 font-semibold">
                                            Đặt vé nhanh
                                            <i class="fa-solid fa-ticket"></i>
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($busFleet->isNotEmpty())
                    <section class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-slate-900">Đội xe</h2>
                            <span class="text-sm text-slate-500">{{ $busFleet->count() }} xe</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach ($busFleet as $bus)
                                <article class="border border-slate-100 rounded-2xl p-5 space-y-3">
                                    <div class="h-40 rounded-xl overflow-hidden bg-slate-100">
                                        <img src="{{ $bus->thumbnail_url ?? '/userfiles/files/kingexpressbus/sleeper/8.jpg' }}" alt="{{ $bus->name }}" class="h-full w-full object-cover" loading="lazy">
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-lg font-semibold text-slate-900">{{ $bus->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $bus->model_name ?? 'Dòng xe tiện nghi' }}</p>
                                        <p class="text-sm text-slate-500">{{ $bus->seat_count ? $bus->seat_count . ' chỗ' : 'Đang cập nhật số chỗ' }}</p>
                                    </div>
                                    @if (!empty($bus->services))
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($bus->services as $service)
                                                <span class="text-xs bg-blue-50 text-blue-600 px-3 py-1 rounded-full">{{ $service }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($upcomingTrips->isNotEmpty())
                    <section class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-slate-900">Chuyến sắp chạy</h2>
                            <span class="text-sm text-slate-500">{{ $upcomingTrips->count() }} chuyến</span>
                        </div>
                        <div class="space-y-4">
                            @foreach ($upcomingTrips as $trip)
                                <article class="border border-slate-100 rounded-2xl px-5 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $trip->route_name }}</p>
                                        <p class="text-sm text-slate-500">{{ $trip->company_route_name }}</p>
                                    </div>
                                    <div class="text-sm text-slate-500 space-y-1">
                                        <p><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($trip->start_time)->format('d/m H:i') }} - {{ \Carbon\Carbon::parse($trip->end_time)->format('H:i') }}</p>
                                        <p class="font-semibold text-blue-600">{{ $trip->price ? number_format($trip->price) . ' đ' : 'Liên hệ giá' }}</p>
                                    </div>
                                    <a href="{{ route('client.booking.create', ['bus_route_id' => $trip->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-500 transition">
                                        <i class="fa-solid fa-ticket"></i>
                                        Đặt vé
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="space-y-6">

                <div class="bg-slate-900 text-white rounded-3xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold">Liên hệ nhà xe</h3>
                    <p class="text-sm text-white/80">Đặt vé theo đoàn hoặc cần hỗ trợ riêng, hãy liên hệ trực tiếp.</p>
                    @if ($company->hotline)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->hotline) }}" class="inline-flex items-center gap-3 px-5 py-3 bg-yellow-400 text-slate-900 font-semibold rounded-lg shadow hover:bg-yellow-300 transition">
                            <i class="fa-solid fa-phone"></i>
                            {{ $company->hotline }}
                        </a>
                    @endif
                    @if ($company->email)
                        <a href="mailto:{{ $company->email }}" class="inline-flex items-center gap-2 text-sm text-white/80 hover:text-white">
                            <i class="fa-solid fa-envelope"></i>
                            {{ $company->email }}
                        </a>
                    @endif
                </div>

                @if ($routes->isNotEmpty())
                    <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-4 text-sm text-slate-600">
                        <h3 class="text-base font-semibold text-slate-900">Tuyến phổ biến</h3>
                        <ul class="space-y-2">
                            @foreach ($routes->take(5) as $route)
                                <li>
                                    <a href="{{ route('client.routes.show', ['slug' => $route->route_slug]) }}" class="text-blue-600 hover:text-blue-700">
                                        {{ $route->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</x-client.layout>
