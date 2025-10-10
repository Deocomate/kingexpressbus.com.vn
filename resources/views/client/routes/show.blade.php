{{-- ===== resources\views\client\routes\show.blade.php ===== --}}
<x-client.layout :web-profile="$web_profile ?? null" :main-menu="$mainMenu ?? []"
                 :title="$title ?? __('client.route_show.meta_title')" :description="$description ?? ''">
    @php
        $heroImage = $route->banner_url ?? ($route->thumbnail_url ?? '/userfiles/files/city_imgs/ha-noi.jpg');
        $minPrice = (int) ($route->min_price ?? 0);
        $priceDisplay = $minPrice > 0 ? __('client.route_show.price_from', ['price' => number_format($minPrice) . 'đ']) : __('client.route_show.price_contact');
        $routeHighlights = [
            [
                'icon' => 'fa-solid fa-location-dot',
                'label' => __('client.route_show.hero_origin'),
                'value' => $route->start_province_name,
            ],
            [
                'icon' => 'fa-solid fa-map-marker-alt',
                'label' => __('client.route_show.hero_destination'),
                'value' => $route->end_province_name,
            ],
            [
                'icon' => 'fa-solid fa-bus',
                'label' => __('client.route_show.hero_operators'),
                'value' => __('client.route_show.hero_operator_count', ['count' => $route->company_count ?? $companyRoutes->count()]),
            ],
        ];

        $filterKeys = [
            'sort',
            'price_min',
            'price_max',
            'services',
            'pickup_points',
            'dropoff_points',
            'bus_categories',
            'time_ranges',
        ];
        $filterDefaults = [
            'sort' => 'recommended',
            'price_min' => null,
            'price_max' => null,
            'services' => [],
            'pickup_points' => [],
            'dropoff_points' => [],
            'bus_categories' => [],
            'time_ranges' => [],
        ];

        $filterState = array_merge($filterDefaults, $filterState ?? []);
        $filters = $filters ?? [];
        $tripStats = $tripStats ?? ['total' => $trips->count(), 'filtered' => $trips->count()];
        $activeFilterCount = $activeFilterCount ?? 0;
        $hasActiveFilters = $hasActiveFilters ?? $activeFilterCount > 0;

        $persistedQuery = collect(request()->query())->except($filterKeys);
        if (!$persistedQuery->has('departure_date')) {
            $persistedQuery = $persistedQuery->put('departure_date', $departureDate);
        }
        $clearFiltersUrl = route(
            'client.routes.show',
            array_merge(['slug' => $route->slug], $persistedQuery->toArray()),
        );

        $availableServices = collect($filters['services'] ?? [])
            ->filter()
            ->values();
        $pickupOptions = collect($filters['pickup_points'] ?? [])
            ->filter()
            ->values();
        $dropoffOptions = collect($filters['dropoff_points'] ?? [])
            ->filter()
            ->values();
        $busCategoryOptions = collect($filters['bus_categories'] ?? [])
            ->filter()
            ->values();
        $timeRangeOptions = collect($filters['time_ranges'] ?? []);
        $priceRange = $filters['price'] ?? ['min' => null, 'max' => null];
        $sortOptions = [
            'recommended' => __('client.route_show.filters.sort_recommended'),
            'earliest' => __('client.route_show.filters.sort_earliest'),
            'latest' => __('client.route_show.filters.sort_latest'),
            'price_low' => __('client.route_show.filters.sort_price_low'),
            'price_high' => __('client.route_show.filters.sort_price_high'),
            'seats_available' => __('client.route_show.filters.sort_seats'),
        ];
        $galleryFallback = '/userfiles/files/king/sleeper/5.jpg';
    @endphp

    @push('styles')
        <style>
            .filters-card {
                background-color: #ffffff;
                border-radius: 24px;
                border: 1px solid #e5e7eb;
                padding: 24px;
                box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            }

            .filter-input {
                width: 100%;
                border-radius: 14px;
                border: 1px solid #bfdbfe;
                background-color: #f8fafc;
                padding: 10px 14px;
                font-size: 14px;
                transition: all 0.2s ease;
            }

            .filter-input:focus {
                outline: none;
                border-color: #60a5fa;
                box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.25);
                background-color: #ffffff;
            }

            .filter-pill {
                position: relative;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 7px 14px;
                border-radius: 9999px;
                border: 1px solid rgba(191, 219, 254, 0.9);
                background-color: #f1f5f9;
                color: #1d4ed8;
                font-size: 13px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .filter-pill input {
                position: absolute;
                inset: 0;
                opacity: 0;
                cursor: pointer;
            }

            .filter-pill span {
                pointer-events: none;
            }

            .filter-pill input:checked + span {
                background-color: #2563eb;
                color: #ffffff;
                border-radius: 9999px;
                padding: 4px 10px;
            }

            .filter-pill:hover {
                border-color: #60a5fa;
            }

            .trip-card {
                display: flex;
                flex-direction: column;
                gap: 16px;
                background-color: #ffffff;
                border: 1px solid #e2e8f0;
                border-radius: 20px;
                padding: 18px;
                transition: all 0.25s ease;
            }

            .trip-card:hover {
                box-shadow: 0 20px 40px rgba(15, 23, 42, 0.1);
                border-color: #3b82f6;
            }

            .trip-card-media {
                position: relative;
                overflow: hidden;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
                flex-shrink: 0;
                height: 180px;
                width: 100%;
            }

            .trip-card-media img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .scrollbar-thin {
                scrollbar-width: thin;
            }

            .scrollbar-thin::-webkit-scrollbar {
                height: 4px;
            }

            .scrollbar-thin::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 4px;
            }

            .scrollbar-thin::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 4px;
            }

            .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .line-clamp-1 {
                display: -webkit-box;
                -webkit-line-clamp: 1;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .availability-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                padding: 4px 10px;
                border-radius: 9999px;
                font-size: 11px;
                font-weight: 600;
                white-space: nowrap;
            }

            .availability-badge--available {
                background-color: #dcfce7;
                color: #047857;
            }

            .availability-badge--unavailable {
                background-color: #fee2e2;
                color: #b91c1c;
            }

            .price-text {
                color: #047857;
            }

            .mobile-filter-open {
                position: fixed;
                inset: 0;
                background-color: #ffffff;
                overflow-y: auto;
                z-index: 110;
                border-radius: 0;
                padding: 28px 20px 32px;
            }

            #filter-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.45);
                z-index: 100;
            }

            .modal-gallery {
                display: flex;
                gap: 12px;
                overflow-x: auto;
                padding-bottom: 6px;
            }

            .modal-thumb {
                border: 2px solid transparent;
                border-radius: 14px;
                overflow: hidden;
                width: 82px;
                height: 82px;
                flex: 0 0 auto;
                transition: border-color 0.2s ease;
            }

            .modal-thumb img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .modal-thumb.is-active {
                border-color: #2563eb;
            }

            @media (max-width: 1023px) {
                .filters-card {
                    box-shadow: none;
                    border-radius: 16px;
                    padding: 18px;
                }
            }

            @media (min-width: 1024px) {
                .trip-card {
                    flex-direction: row;
                    gap: 18px;
                    padding: 20px;
                }

                .trip-card-media {
                    width: 220px;
                    min-width: 220px;
                    max-width: 220px;
                    height: 165px;
                }

                .trip-card-body {
                    flex: 1;
                    min-width: 0;
                }
            }

            @media (max-width: 640px) {
                .trip-card {
                    padding: 16px;
                    gap: 14px;
                }

                .trip-card-media {
                    height: 160px;
                    width: 100%;
                }
            }
        </style>
    @endpush
    <section class="relative bg-gray-900 text-white">
        <div class="absolute inset-0">
            <img src="{{ $heroImage }}" alt="{{ $route->name }}" class="h-full w-full object-cover" loading="lazy">
            <div class="absolute inset-0 bg-slate-900/70"></div>
        </div>
        <div class="relative container mx-auto px-4 py-16 lg:py-24 space-y-8">
            <div class="max-w-3xl space-y-5">
                <span
                    class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-wider text-yellow-300">
                    <i class="fa-solid fa-map-location-dot"></i>{{ __('client.route_show.hero_brand') }}
                </span>
                <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">{{ $route->name }}</h1>
                <p class="text-lg text-white/85 max-w-2xl">{{ $route->description }}</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($routeHighlights as $highlight)
                    <div
                        class="rounded-2xl bg-white/10 backdrop-blur p-4 text-center sm:text-left sm:flex sm:items-center sm:gap-4">
                        <i class="{{ $highlight['icon'] }} text-yellow-300 text-2xl"></i>
                        <div>
                            <p class="font-semibold text-white">{{ $highlight['value'] }}</p>
                            <p class="text-sm text-white/70">{{ $highlight['label'] }}</p>
                        </div>
                    </div>
                @endforeach
                <div
                    class="rounded-2xl bg-white/10 backdrop-blur p-4 text-center sm:text-left sm:flex sm:items-center sm:gap-4">
                    <i class="fa-solid fa-tag text-yellow-300 text-2xl"></i>
                    <div>
                        <div class="font-semibold">{{ __('client.route_show.hero_price_label') }}</div>
                        <div class="text-white/80 text-sm">{{ $priceDisplay }}</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="search-section" class="bg-gray-100 py-6">
        <section class="container mx-auto px-4">
            <div class="bg-white shadow-lg rounded-3xl p-4 md:p-5 border border-gray-200">
                <x-client.search-bar :search-data="$searchData"
                                     :submit-label="__('client.route_show.search_submit_label')"/>
            </div>
        </section>
    </div>

    @if ($trips->isNotEmpty())
        <section id="availabilities" class="py-16 bg-gray-50">
            <div class="container mx-auto px-4 space-y-8">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                    <div class="space-y-2">
                        <h2 class="text-3xl font-bold text-gray-900">{{ __('client.route_show.results_title') }}</h2>
                        <p class="text-gray-600">{{ __('client.route_show.results_subtitle', ['filtered' => $tripStats['filtered'], 'total' => $tripStats['total'], 'date' => $departureDate]) }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button data-filter-toggle
                                class="lg:hidden inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-full font-semibold text-gray-700 hover:bg-gray-100 transition">
                            <i class="fa-solid fa-filter"></i>
                            <span>{{ __('client.route_show.filters.mobile_button') }}</span>
                            @if ($hasActiveFilters)
                                <span class="w-2.5 h-2.5 bg-blue-600 rounded-full"></span>
                            @endif
                        </button>
                    </div>
                </div>

                <div id="filter-backdrop" class="hidden lg:hidden"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                    <aside class="lg:col-span-3">
                        <div id="filter-panel"
                             class="filters-card fixed inset-0 z-[110] transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:transform-none lg:z-auto lg:inset-auto bg-white p-6 lg:p-0 overflow-y-auto lg:overflow-visible">
                            <div class="flex justify-between items-center lg:hidden mb-5">
                                <h3 class="text-xl font-bold">{{ __('client.route_show.filters.mobile_title') }}</h3>
                                <button data-filter-close
                                        class="text-gray-500 hover:text-gray-700 text-2xl">&times;
                                </button>
                            </div>

                            <form id="filter-form" action="{{ $clearFiltersUrl }}" method="GET" class="space-y-6">
                                <div class="space-y-5 p-5">
                                    <div>
                                        <h3
                                            class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-arrow-down-wide-short text-blue-500"></i>{{ __('client.route_show.filters.sort_title') }}
                                        </h3>
                                        <div class="space-y-2">
                                            @foreach ($sortOptions as $value => $label)
                                                <label class="flex items-center gap-3 text-sm text-gray-700">
                                                    <input type="radio" name="sort" value="{{ $value }}"
                                                           @checked(($filterState['sort'] ?? 'recommended') === $value)
                                                           class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="pt-5 border-t border-gray-200">
                                        <h3
                                            class="text-sm font-semibold text-emerald-600 uppercase tracking-wide mb-3 flex items-center gap-2">
                                            <i class="fa-solid fa-money-bill-wave text-emerald-500"></i>{{ __('client.route_show.filters.price_title') }}
                                        </h3>
                                        <div class="flex items-center gap-3">
                                            <input type="number" name="price_min"
                                                   value="{{ $filterState['price_min'] }}"
                                                   placeholder="{{ $priceRange['min'] ? number_format($priceRange['min']) : __('client.route_show.filters.price_from') }}"
                                                   class="filter-input text-sm" min="0" inputmode="numeric">
                                            <span class="text-gray-400 text-sm">-</span>
                                            <input type="number" name="price_max"
                                                   value="{{ $filterState['price_max'] }}"
                                                   placeholder="{{ $priceRange['max'] ? number_format($priceRange['max']) : __('client.route_show.filters.price_to') }}"
                                                   class="filter-input text-sm" min="0" inputmode="numeric">
                                        </div>
                                        @if ($priceRange['min'] && $priceRange['max'])
                                            <div
                                                class="text-xs text-gray-500 mt-2">{{ __('client.route_show.filters.price_range_note') }}
                                                {{ number_format($priceRange['min']) }}đ -
                                                {{ number_format($priceRange['max']) }}đ
                                            </div>
                                        @endif
                                    </div>

                                    @if ($timeRangeOptions->isNotEmpty())
                                        <div class="pt-5 border-t border-gray-200">
                                            <h3
                                                class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                                                <i class="fa-solid fa-clock text-amber-500"></i>{{ __('client.route_show.filters.time_range_title') }}
                                            </h3>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($timeRangeOptions as $key => $range)
                                                    <label class="filter-pill">
                                                        <input type="checkbox" name="time_ranges[]"
                                                               value="{{ $key }}" @checked(in_array($key, $filterState['time_ranges'] ?? []))>
                                                        <span>{{ $range['label'] ?? $key }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($availableServices->isNotEmpty())
                                        <div class="pt-5 border-t border-gray-200">
                                            <h3
                                                class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                                                <i class="fa-solid fa-star text-yellow-500"></i>{{ __('client.route_show.filters.services_title') }}
                                            </h3>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($availableServices as $service)
                                                    <label class="filter-pill">
                                                        <input type="checkbox" name="services[]"
                                                               value="{{ $service }}" @checked(in_array($service, $filterState['services'] ?? []))>
                                                        <span>{{ $service }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($busCategoryOptions->isNotEmpty())
                                        <div class="pt-5 border-t border-gray-200">
                                            <h3
                                                class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                                                <i class="fa-solid fa-van-shuttle text-purple-500"></i>{{ __('client.route_show.filters.bus_type_title') }}
                                            </h3>
                                            <div class="space-y-2">
                                                @foreach ($busCategoryOptions as $category)
                                                    <label class="flex items-center gap-3 text-sm text-gray-700">
                                                        <input type="checkbox" name="bus_categories[]"
                                                               value="{{ $category }}"
                                                               @checked(in_array($category, $filterState['bus_categories'] ?? []))
                                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <span>{{ $category }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($pickupOptions->isNotEmpty())
                                        <div class="pt-5 border-t border-gray-200">
                                            <h3
                                                class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                                                <i class="fa-solid fa-location-dot text-rose-500"></i>{{ __('client.route_show.filters.pickup_title') }}
                                            </h3>
                                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                                @foreach ($pickupOptions as $pickup)
                                                    <label class="flex items-center gap-3 text-sm text-gray-700">
                                                        <input type="checkbox" name="pickup_points[]"
                                                               value="{{ $pickup }}"
                                                               @checked(in_array($pickup, $filterState['pickup_points'] ?? []))
                                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <span class="line-clamp-2">{{ $pickup }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if ($dropoffOptions->isNotEmpty())
                                        <div class="pt-5 border-t border-gray-200">
                                            <h3
                                                class="text-sm font-semibold text-gray-900 uppercase tracking-wide mb-3 flex items-center gap-2">
                                                <i class="fa-solid fa-flag-checkered text-green-500"></i>{{ __('client.route_show.filters.dropoff_title') }}
                                            </h3>
                                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                                @foreach ($dropoffOptions as $dropoff)
                                                    <label class="flex items-center gap-3 text-sm text-gray-700">
                                                        <input type="checkbox" name="dropoff_points[]"
                                                               value="{{ $dropoff }}"
                                                               @checked(in_array($dropoff, $filterState['dropoff_points'] ?? []))
                                                               class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                        <span class="line-clamp-2">{{ $dropoff }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-5 border-t border-gray-200 space-y-3 p-4">
                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
                                        <i class="fa-solid fa-check"></i>{{ __('client.route_show.filters.apply_button') }}
                                    </button>
                                    <a href="{{ $clearFiltersUrl }}"
                                       class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-100 transition">
                                        <i class="fa-solid fa-rotate-left"></i>{{ __('client.route_show.filters.clear_button') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </aside>

                    <div class="lg:col-span-9 space-y-6">
                        @foreach ($trips as $trip)
                            @php
                                $tripStart = \Carbon\Carbon::createFromFormat('H:i:s', $trip->start_time);
                                $tripEnd = \Carbon\Carbon::createFromFormat('H:i:s', $trip->end_time);
                                $pickupPoints = collect($trip->pickup_points ?? []);
                                $dropoffPoints = collect($trip->dropoff_points ?? []);
                                $firstPickup = $pickupPoints->first();
                                $firstDropoff = $dropoffPoints->first();
                                $imageGallery = collect($trip->image_gallery ?? ($trip->bus_images ?? []))
                                    ->filter()
                                    ->values();
                                if ($imageGallery->isEmpty() && $trip->bus_thumbnail) {
                                    $imageGallery = collect([$trip->bus_thumbnail]);
                                }
                                $primaryImage =
                                    $trip->primary_bus_image ?? ($imageGallery->first() ?: $galleryFallback);
                                $durationMinutes = $trip->duration_minutes ?? 0;
                                $durationLabel =
                                    $durationMinutes > 0
                                        ? __('client.route_show.trip_card.duration_format', ['hours' => intdiv($durationMinutes, 60), 'minutes' => $durationMinutes % 60])
                                        : __('client.route_show.trip_card.duration_format', ['hours' => (int) $tripStart->diff($tripEnd)->format('%h'), 'minutes' => (int) $tripStart->diff($tripEnd)->format('%i')]);
                                $serviceList = collect($trip->services ?? [])
                                    ->filter()
                                    ->values();
                                $hasSeats = ($trip->seats_available ?? 0) > 0;
                            @endphp
                            <article class="trip-card">
                                {{-- Left: Bus Image --}}
                                <div class="trip-card-media">
                                    <img id="trip-image-{{ $trip->bus_route_id }}" src="{{ $primaryImage }}"
                                         alt="{{ $trip->company_name }}" loading="lazy">
                                    <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/95 text-gray-800 text-xs font-semibold rounded-full shadow-sm backdrop-blur">
                                        <i class="fa-solid fa-bus text-xs"></i>
                                        {{ $trip->bus_category }}
                                    </span>
                                </div>

                                {{-- Right: Trip Information --}}
                                <div class="trip-card-body flex-1 flex flex-col">
                                    {{-- Header: Company Info + Price --}}
                                    <div class="flex items-start justify-between gap-4 mb-3">
                                        <div class="flex items-start gap-3 flex-1 min-w-0">
                                            <img src="{{ $trip->company_thumbnail ?: '/userfiles/files/web information/logo.jpg' }}"
                                                 alt="{{ $trip->company_name }}"
                                                 class="h-11 w-11 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <h3 class="text-base font-bold text-gray-900 truncate">{{ $trip->company_name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $trip->bus_name }}</p>
                                                <p class="text-xs text-gray-400">Mã: {{ $trip->bus_route_id }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            @if ($trip->has_price)
                                                <p class="text-xl font-bold price-text leading-tight">{{ number_format($trip->price_value) }}đ</p>
                                                <span class="availability-badge {{ $trip->seats_available > 0 ? 'availability-badge--available' : 'availability-badge--unavailable' }} mt-1.5">
                                                    <i class="fa-solid fa-circle text-[6px]"></i>
                                                    <span>{{ $trip->seats_available > 0 ? __('client.route_show.trip_card.seats_available') : __('client.route_show.trip_card.seats_full') }}</span>
                                                </span>
                                            @else
                                                <p class="text-lg font-bold text-blue-600">{{ __('client.route_show.price_contact') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Time & Duration --}}
                                    <div class="grid grid-cols-3 gap-3 py-3 border-y border-gray-100">
                                        <div>
                                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $tripStart->format('H:i') }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1" title="{{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}">
                                                {{ $firstPickup->name ?? __('client.route_show.trip_card.pickup_point') }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-400">{{ __('client.route_show.trip_card.duration_label') }}</p>
                                            <p class="text-xs text-gray-700 font-semibold mt-0.5">{{ $durationLabel }}</p>
                                            <div class="relative h-px bg-gray-300 mt-1.5">
                                                <i class="fa-solid fa-bus-simple absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 bg-white px-1.5 text-gray-400 text-xs"></i>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-gray-900 leading-tight">{{ $tripEnd->format('H:i') }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-1" title="{{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}">
                                                {{ $firstDropoff->name ?? __('client.route_show.trip_card.dropoff_point') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Pickup & Dropoff Points (Compact) --}}
                                    <div class="grid grid-cols-2 gap-3 my-3">
                                        <div class="bg-blue-50/50 rounded-lg p-2.5 border border-blue-100/50">
                                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                                <i class="fa-solid fa-location-dot text-blue-600 text-xs"></i>
                                                Điểm đón
                                            </h4>
                                            @forelse ($pickupPoints->take(2) as $pickup)
                                                <p class="text-xs text-gray-600 truncate">• {{ $pickup->name }}</p>
                                            @empty
                                                <p class="text-xs text-gray-400">Chưa cập nhật</p>
                                            @endforelse
                                            @if ($pickupPoints->count() > 2)
                                                <p class="text-xs text-blue-600 font-medium mt-0.5">+{{ $pickupPoints->count() - 2 }} điểm khác</p>
                                            @endif
                                        </div>
                                        <div class="bg-emerald-50/50 rounded-lg p-2.5 border border-emerald-100/50">
                                            <h4 class="text-xs font-semibold text-gray-700 mb-1.5 flex items-center gap-1.5">
                                                <i class="fa-solid fa-flag-checkered text-emerald-600 text-xs"></i>
                                                Điểm trả
                                            </h4>
                                            @forelse ($dropoffPoints->take(2) as $dropoff)
                                                <p class="text-xs text-gray-600 truncate">• {{ $dropoff->name }}</p>
                                            @empty
                                                <p class="text-xs text-gray-400">Chưa cập nhật</p>
                                            @endforelse
                                            @if ($dropoffPoints->count() > 2)
                                                <p class="text-xs text-emerald-600 font-medium mt-0.5">+{{ $dropoffPoints->count() - 2 }} điểm khác</p>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Services (Compact) --}}
                                    <div class="flex flex-wrap gap-1.5 mb-3">
                                        @forelse ($serviceList->take(4) as $service)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-medium">
                                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                                {{ $service }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Chưa có tiện ích</span>
                                        @endforelse
                                        @if ($serviceList->count() > 4)
                                            <span class="text-[11px] text-blue-600 font-medium">+{{ $serviceList->count() - 4 }}</span>
                                        @endif
                                    </div>

                                    {{-- Gallery Thumbnails (if multiple images) --}}
                                    @if ($imageGallery->count() > 1)
                                        <div class="flex gap-1.5 overflow-x-auto pb-2 mb-3 scrollbar-thin">
                                            @foreach ($imageGallery->take(5) as $image)
                                                <button type="button" data-image-trigger
                                                        data-target="#trip-image-{{ $trip->bus_route_id }}"
                                                        data-image="{{ $image }}"
                                                        class="flex-shrink-0 w-12 h-12 rounded-lg overflow-hidden border border-gray-200 hover:border-blue-400 transition">
                                                    <img src="{{ $image }}" alt="Bus image" loading="lazy" class="w-full h-full object-cover">
                                                </button>
                                            @endforeach
                                            @if ($imageGallery->count() > 5)
                                                <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-xs font-semibold text-gray-600">
                                                    +{{ $imageGallery->count() - 5 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-2.5 mt-auto pt-2">
                                        <a href="{{ route('client.booking.create', ['bus_route_id' => $trip->bus_route_id, 'date' => $departureDate]) }}"
                                           class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-yellow-400 text-gray-900 rounded-xl font-semibold hover:bg-yellow-500 transition text-sm shadow-sm">
                                            <i class="fa-solid fa-ticket text-sm"></i>Chọn chuyến
                                        </a>
                                        <button type="button"
                                                class="inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition text-sm view-trip-details-btn"
                                                data-trip='{{ json_encode($trip) }}'>
                                            <i class="fa-solid fa-circle-info text-sm"></i>Xem chi tiết
                                        </button>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @else
        <section class="py-16 bg-gray-50 text-center">
            <div class="container mx-auto px-4 space-y-4">
                <i class="fa-solid fa-calendar-times text-5xl text-gray-400"></i>
                <h2 class="text-2xl font-bold text-gray-800">{{ __('client.route_show.no_trips.title') }}</h2>
                <p class="text-gray-600">{{ __('client.route_show.no_trips.description') }}
                </p>
                <div class="flex justify-center gap-3">
                    <a href="#search-section"
                       class="inline-flex items-center gap-2 px-4 py-2 border border-blue-600 text-blue-600 rounded-full font-semibold hover:bg-blue-50 transition">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>{{ __('client.route_show.no_trips.research_button') }}</span>
                    </a>
                    @if ($hasActiveFilters ?? false)
                        <a href="{{ $clearFiltersUrl }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full font-semibold hover:bg-blue-700 transition">
                            <i class="fa-solid fa-xmark"></i>
                            <span>{{ __('client.route_show.no_trips.clear_filters_button') }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif
    @if (!empty($travelTips))
        <section id="travel-tips" class="py-16 bg-white">
            <div class="container mx-auto px-4 space-y-8">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-lightbulb text-yellow-500 text-3xl"></i>
                    <h2 class="text-3xl font-bold text-gray-900">{{ __('client.route_show.tips.title') }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($travelTips as $tip)
                        <article class="bg-gray-50 border border-gray-100 rounded-2xl p-6">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $tip['title'] }}</h3>
                            <p class="mt-3 text-base text-gray-600 leading-relaxed">{{ $tip['content'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div id="trip-details-modal"
         class="hidden fixed inset-0 bg-black bg-opacity-60 z-[120] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[92vh] flex flex-col">
            <div class="flex justify-between items-center p-5 border-b">
                <h3 class="text-xl font-bold text-gray-900">{{ __('client.route_show.details_modal.title') }}</h3>
                <button id="close-modal-btn" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3">{{ __('client.route_show.details_modal.bus_info_title') }}</h4>
                        <img id="modal-bus-image" src="" alt="{{__('client.route_show.details_modal.bus_image_alt')}}"
                             class="w-full h-48 object-cover rounded-lg mb-4">
                        <div id="modal-gallery" class="modal-gallery"></div>
                        <ul class="space-y-2 text-base mt-4">
                            <li><strong>{{ __('client.route_show.details_modal.company') }}</strong> <span
                                    id="modal-company-name"></span></li>
                            <li><strong>{{ __('client.route_show.details_modal.bus_type') }}</strong> <span
                                    id="modal-bus-category"></span></li>
                            <li><strong>{{ __('client.route_show.details_modal.bus_details') }}</strong> <span
                                    id="modal-bus-name"></span> (<span
                                    id="modal-bus-model"></span>)
                            </li>
                        </ul>
                        <h4 class="font-semibold text-gray-800 mt-4 mb-2">{{ __('client.route_show.details_modal.services_title') }}</h4>
                        <div id="modal-services" class="flex flex-wrap gap-2"></div>
                    </div>
                    <div class="space-y-4">
                        <h4 class="font-semibold text-gray-800">{{ __('client.route_show.details_modal.seat_map_title') }}</h4>
                        <div id="seat-map-container" class="space-y-4">
                            <div class="flex flex-wrap gap-4 mb-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded bg-white border border-gray-300"></div>
                                    <span>{{ __('client.route_show.details_modal.seat_available') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-5 h-5 rounded bg-gray-400 text-white flex items-center justify-center">
                                        <i class="fa-solid fa-lock text-xs"></i>
                                    </div>
                                    <span>{{ __('client.route_show.details_modal.seat_booked') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="w-5 h-5 rounded bg-blue-500 border border-blue-600"></div>
                                    <span>{{ __('client.route_show.details_modal.seat_selected') }}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                            </div>
                        </div>
                        <h4 class="font-semibold text-gray-800">{{ __('client.route_show.details_modal.stops_info_title') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <h5 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ __('client.route_show.details_modal.pickup_points_title') }}</h5>
                                <ul id="modal-pickup-points" class="space-y-1 text-sm text-gray-600"></ul>
                            </div>
                            <div>
                                <h5 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ __('client.route_show.details_modal.dropoff_points_title') }}</h5>
                                <ul id="modal-dropoff-points" class="space-y-1 text-sm text-gray-600"></ul>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">{{ __('client.route_show.details_modal.status') }} <span
                                    id="modal-availability"
                                    class="font-semibold text-emerald-600"></span>
                            </p>
                        </div>
                        <div class="pt-4 border-t">
                            <a id="modal-booking-link" href="#"
                               class="w-full text-center px-5 py-3 bg-yellow-400 text-gray-900 font-bold rounded-full hover:bg-yellow-500 transition block">{{ __('client.route_show.details_modal.book_now_button') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const body = document.body;
                const filterPanel = document.getElementById('filter-panel');
                const filterBackdrop = document.getElementById('filter-backdrop');

                function openFilters() {
                    if (!filterPanel) return;
                    filterPanel.classList.remove('hidden');
                    filterPanel.classList.add('mobile-filter-open');
                    if (filterBackdrop) {
                        filterBackdrop.classList.remove('hidden');
                    }
                    body.classList.add('overflow-hidden');
                }

                function closeFilters() {
                    if (!filterPanel) return;
                    filterPanel.classList.remove('mobile-filter-open');
                    if (window.innerWidth < 1024) {
                        filterPanel.classList.add('hidden');
                    }
                    if (filterBackdrop) {
                        filterBackdrop.classList.add('hidden');
                    }
                    body.classList.remove('overflow-hidden');
                }

                document.querySelectorAll('[data-filter-toggle]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        openFilters();
                    });
                });

                document.querySelectorAll('[data-filter-close]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        closeFilters();
                    });
                });

                if (filterBackdrop) {
                    filterBackdrop.addEventListener('click', closeFilters);
                }

                window.addEventListener('resize', function () {
                    if (window.innerWidth >= 1024) {
                        body.classList.remove('overflow-hidden');
                        if (filterBackdrop) {
                            filterBackdrop.classList.add('hidden');
                        }
                        if (filterPanel) {
                            filterPanel.classList.remove('mobile-filter-open');
                        }
                    }
                });

                document.querySelectorAll('[data-image-trigger]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const targetSelector = button.getAttribute('data-target');
                        const imageUrl = button.getAttribute('data-image');
                        const target = document.querySelector(targetSelector);
                        if (target && imageUrl) {
                            target.setAttribute('src', imageUrl);
                        }
                    });
                });

                const modal = document.getElementById('trip-details-modal');
                const closeModalBtn = document.getElementById('close-modal-btn');
                const seatMapContainer = document.getElementById('seat-map-container');
                const modalCompanyName = document.getElementById('modal-company-name');
                const modalBusName = document.getElementById('modal-bus-name');
                const modalBusModel = document.getElementById('modal-bus-model');
                const modalBusCategory = document.getElementById('modal-bus-category');
                const modalServices = document.getElementById('modal-services');
                const modalBusImage = document.getElementById('modal-bus-image');
                const modalGallery = document.getElementById('modal-gallery');
                const modalPickupPoints = document.getElementById('modal-pickup-points');
                const modalDropoffPoints = document.getElementById('modal-dropoff-points');
                const modalAvailability = document.getElementById('modal-availability');
                const modalBookingLink = document.getElementById('modal-booking-link');

                function openModal() {
                    modal.classList.remove('hidden');
                    body.classList.add('overflow-hidden');
                }

                function closeModal() {
                    modal.classList.add('hidden');
                    body.classList.remove('overflow-hidden');
                }

                if (closeModalBtn) {
                    closeModalBtn.addEventListener('click', closeModal);
                }

                if (modal) {
                    modal.addEventListener('click', function (event) {
                        if (event.target === modal) {
                            closeModal();
                        }
                    });
                }

                document.querySelectorAll('.view-trip-details-btn').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const rawData = button.getAttribute('data-trip');
                        if (!rawData) return;
                        const tripData = JSON.parse(rawData);
                        if (!tripData) return;

                        modalCompanyName.textContent = tripData.company_name || '';
                        modalBusName.textContent = tripData.bus_name || '';
                        modalBusModel.textContent = tripData.bus_model || '';
                        modalBusCategory.textContent = tripData.bus_category || "{{__('client.route_show.details_modal.not_updated')}}";

                        const galleryImages = Array.isArray(tripData.image_gallery) ? tripData
                            .image_gallery : [];
                        let initialImage = tripData.primary_bus_image || galleryImages[0] || tripData
                            .bus_thumbnail || '{{ $galleryFallback }}';
                        modalBusImage.src = initialImage;

                        modalGallery.innerHTML = '';
                        if (galleryImages.length > 0) {
                            let activeThumb = null;
                            galleryImages.forEach(function (src) {
                                const thumbBtn = document.createElement('button');
                                thumbBtn.type = 'button';
                                thumbBtn.className = 'modal-thumb';
                                thumbBtn.innerHTML = '<img src="' + src + '" alt="{{__('client.route_show.details_modal.bus_image_alt')}}">';
                                if (src === initialImage) {
                                    thumbBtn.classList.add('is-active');
                                    activeThumb = thumbBtn;
                                }
                                thumbBtn.addEventListener('click', function () {
                                    modalBusImage.src = src;
                                    if (activeThumb) {
                                        activeThumb.classList.remove('is-active');
                                    }
                                    thumbBtn.classList.add('is-active');
                                    activeThumb = thumbBtn;
                                });
                                modalGallery.appendChild(thumbBtn);
                                if (!activeThumb) {
                                    thumbBtn.classList.add('is-active');
                                    activeThumb = thumbBtn;
                                }
                            });
                        } else {
                            modalGallery.innerHTML =
                                '<p class="text-sm text-gray-500">' + "{{__('client.route_show.details_modal.no_gallery')}}" + '</p>';
                        }

                        modalServices.innerHTML = '';
                        if (tripData.services && tripData.services.length > 0) {
                            tripData.services.forEach(function (service) {
                                const chip = document.createElement('span');
                                chip.className =
                                    'inline-flex items-center gap-2 px-3 py-1 text-sm font-semibold rounded-full bg-blue-50 text-blue-700';
                                chip.innerHTML = '<i class="fa-solid fa-check"></i> ' + service;
                                modalServices.appendChild(chip);
                            });
                        } else {
                            modalServices.innerHTML =
                                '<p class="text-sm text-gray-500">' + "{{__('client.route_show.details_modal.no_services')}}" + '</p>';
                        }

                        modalPickupPoints.innerHTML = '';
                        if (tripData.pickup_points) {
                            tripData.pickup_points.forEach(function (point) {
                                const item = document.createElement('li');
                                item.textContent = point.name || '';
                                modalPickupPoints.appendChild(item);
                            });
                        }

                        modalDropoffPoints.innerHTML = '';
                        if (tripData.dropoff_points) {
                            tripData.dropoff_points.forEach(function (point) {
                                const item = document.createElement('li');
                                item.textContent = point.name || '';
                                modalDropoffPoints.appendChild(item);
                            });
                        }

                        const seatsAvailable = Number(tripData.seats_available ?? 0);
                        modalAvailability.textContent = seatsAvailable > 0 ? "{{__('client.route_show.trip_card.seats_available')}}" : "{{__('client.route_show.trip_card.seats_full')}}";

                        const bookedSeats = Array.isArray(tripData.booked_seats) ? tripData
                            .booked_seats : [];
                        generateSeatMap(tripData.seat_map, bookedSeats);

                        const bookingUrl = new URL("{{ route('client.booking.create') }}", window
                            .location.origin);
                        bookingUrl.searchParams.set('bus_route_id', tripData.bus_route_id);
                        bookingUrl.searchParams.set('date', '{{ $departureDate }}');
                        modalBookingLink.href = bookingUrl.toString();

                        openModal();
                    });
                });

                function generateSeatMap(seatMapData, bookedSeats = []) {
                    seatMapContainer.innerHTML = '';
                    if (!seatMapData) {
                        seatMapContainer.innerHTML =
                            '<p class="text-center text-gray-500">' + "{{__('client.route_show.details_modal.no_seat_map')}}" + '</p>';
                        return;
                    }

                    try {
                        const seatMap = typeof seatMapData === 'string' ? JSON.parse(seatMapData) : seatMapData;
                        const booked = bookedSeats.map(function (seat) {
                            return String(seat).toUpperCase();
                        });

                        if (seatMap.floors && seatMap.floors.length > 0) {
                            seatMap.floors.forEach(function (floor) {
                                const deckDiv = document.createElement('div');
                                deckDiv.className = 'seat-deck';
                                deckDiv.innerHTML = '<h5 class="seat-deck-title">' + (floor.label || "{{__('client.route_show.details_modal.deck')}}") + '</h5>';

                                const rows = {};
                                floor.seats.forEach(function (seat) {
                            const rowKey = seat.row;
                            if (!rows[rowKey]) {
                                rows[rowKey] = [];
                            }
                            rows[rowKey].push(seat);
                        });

                        Object.keys(rows).sort().forEach(function (rowKey) {
                            const rowDiv = document.createElement('div');
                            rowDiv.className = 'seat-row';
                            rows[rowKey].sort(function (a, b) {
                                return a.col - b.col;
                            }).forEach(function (seat) {
                                const seatEl = document.createElement('div');
                                if (seat.type === 'aisle') {
                                    seatEl.className = 'seat-aisle';
                                } else {
                                    seatEl.className = 'seat';
                                    seatEl.textContent = seat.code;
                                    const seatCode = String(seat.code).toUpperCase();
                                    if (booked.includes(seatCode)) {
                                        seatEl.classList.add('booked');
                                    } else if (seat.status === 'disabled') {
                                        seatEl.classList.add('disabled');
                                    } else {
                                        seatEl.classList.add('available');
                                    }
                                }
                                rowDiv.appendChild(seatEl);
                            });
                            deckDiv.appendChild(rowDiv);
                        });

                        seatMapContainer.appendChild(deckDiv);
                    });
                }
            } catch (error) {
                seatMapContainer.innerHTML =
                    '<p class="text-center text-gray-500">' + "{{__('client.route_show.details_modal.seat_map_error')}}" + '</p>';
            }
                }
            });
        </script>
    @endpush
</x-client.layout>
