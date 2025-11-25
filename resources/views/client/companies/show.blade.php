<x-client.layout :title="$title ?? __('companies.company_info')" :description="$description ?? ''">
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
                        {{ __('companies.partner_company') }}
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold leading-tight">{{ $company->name }}</h1>
                    <p class="text-white/80 max-w-2xl">{{ $company->description ?? __('companies.default_description') }}</p>
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
                    <p class="text-sm text-white/70">{{ __('companies.routes_count') }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ $statistics['fleet_size'] ?? $busFleet->count() }}</p>
                    <p class="text-sm text-white/70">{{ __('companies.fleet_size') }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ $statistics['active_trip_count'] ?? $upcomingTrips->count() }}</p>
                    <p class="text-sm text-white/70">{{ __('companies.upcoming_trips') }}</p>
                </div>
                <div class="rounded-2xl bg-white/10 backdrop-blur p-5 text-center">
                    <p class="text-2xl font-bold text-white">{{ isset($statistics['min_price']) && $statistics['min_price'] ? number_format($statistics['min_price']) . ' đ' : __('companies.contact') }}</p>
                    <p class="text-sm text-white/70">{{ __('companies.ticket_price_from') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Dedicated route search section under company header -->
    <section id="search-section" class="py-8 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="bg-white border border-slate-100 rounded-3xl p-4 shadow-sm">
                <x-client.search-bar :search-data="$searchData" submit-label="{{ __('companies.find_route') }}" />
            </div>
        </div>
    </section>

    <section class="py-12 bg-slate-50">
        <div class="container mx-auto px-4 grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-8">
                @if ($routes->isNotEmpty())
                    <section class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-slate-900">{{ __('companies.routes_count') }}</h2>
                            <span class="text-sm text-slate-500">{{ $routes->count() }} {{ __('companies.routes_label') }}</span>
                        </div>
                        <div class="space-y-5">
                            @foreach ($routes as $route)
                                <article class="border border-slate-100 rounded-2xl p-5 hover:border-blue-200 hover:bg-blue-50 transition">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div>
                                            <p class="text-lg font-semibold text-slate-900">{{ $route->name }}</p>
                                            <p class="text-sm text-slate-500">{{ $route->description ?? __('companies.route_info_updating') }}</p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600">
                                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-clock"></i>{{ $route->duration ?? __('companies.updating') }}</span>
                                            <span class="inline-flex items-center gap-2"><i class="fa-solid fa-road"></i>{{ $route->distance_km ? $route->distance_km . ' km' : __('companies.distance_updating') }}</span>
                                            <span class="inline-flex items-center gap-2 text-blue-600 font-semibold">
                                                <i class="fa-solid fa-ticket"></i>
                                                {{ $route->min_price ? __('companies.price_from') . ' ' . number_format($route->min_price) . ' đ' : __('companies.contact_price') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                                        <a href="{{ route('client.routes.show', ['slug' => $route->route_slug]) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-semibold">
                                            {{ __('companies.view_route_overview') }}
                                            <i class="fa-solid fa-arrow-right"></i>
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
                            <h2 class="text-2xl font-bold text-slate-900">{{ __('companies.bus_fleet') }}</h2>
                            <span class="text-sm text-slate-500">{{ $busFleet->count() }} {{ __('companies.buses_label') }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            @foreach ($busFleet as $bus)
                                <article class="border border-slate-100 rounded-2xl p-5 space-y-3">
                                    <div class="h-40 rounded-xl overflow-hidden bg-slate-100">
                                        <img src="{{ $bus->thumbnail_url ?? '/userfiles/files/kingexpressbus/sleeper/8.jpg' }}" alt="{{ $bus->name }}" class="h-full w-full object-cover" loading="lazy">
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-lg font-semibold text-slate-900">{{ $bus->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $bus->model_name ?? __('companies.comfortable_bus_model') }}</p>
                                        <p class="text-sm text-slate-500">{{ $bus->seat_count ? $bus->seat_count . ' ' . __('companies.seats') : __('companies.seat_count_updating') }}</p>
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
                            <h2 class="text-2xl font-bold text-slate-900">{{ __('companies.upcoming_trips') }}</h2>
                            <span class="text-sm text-slate-500">{{ $upcomingTrips->count() }} {{ __('companies.trips_label') }}</span>
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
                                        <p class="font-semibold text-blue-600">{{ $trip->price ? number_format($trip->price) . ' đ' : __('companies.contact_price') }}</p>
                                    </div>
                                    <a href="{{ route('client.booking.create', ['bus_route_id' => $trip->id, 'date' => \Carbon\Carbon::parse($trip->start_time)->format('Y-m-d')]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-500 transition">
                                        <i class="fa-solid fa-ticket"></i>
                                        {{ __('companies.book_ticket') }}
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="space-y-6">

                <div class="bg-slate-900 text-white rounded-3xl p-6 space-y-4">
                    <h3 class="text-lg font-semibold">{{ __('companies.contact_company') }}</h3>
                    <p class="text-sm text-white/80">{{ __('companies.contact_desc') }}</p>
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
                        <h3 class="text-base font-semibold text-slate-900">{{ __('companies.popular_routes') }}</h3>
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
