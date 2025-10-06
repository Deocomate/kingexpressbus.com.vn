<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Support\Client\SearchDataBuilder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RouteController extends Controller
{
    public function search(Request $request)
    {
        $validated = $request->validate([
            'origin_id' => 'required|integer',
            'origin_type' => 'required|string|in:province,district,stop',
            'destination_id' => 'required|integer',
            'destination_type' => 'required|string|in:province,district,stop',
            'departure_date' => 'required|date_format:d/m/Y',
            'return_date' => 'nullable|date_format:d/m/Y',
        ]);

        try {
            $startProvinceId = $this->resolveProvinceId($validated['origin_type'], (int)$validated['origin_id']);
            $endProvinceId = $this->resolveProvinceId($validated['destination_type'], (int)$validated['destination_id']);

            if (!$startProvinceId || !$endProvinceId) {
                return $this->searchErrorResponse($request, __('client.route_show.search.invalid_location'));
            }

            $route = DB::table('routes')
                ->where('province_start_id', $startProvinceId)
                ->where('province_end_id', $endProvinceId)
                ->first();

            if (!$route) {
                return $this->searchErrorResponse($request, __('client.route_show.search.no_route_found'));
            }

            $params = [
                'slug' => $route->slug,
                'departure_date' => $validated['departure_date'],
                'origin_id' => $validated['origin_id'],
                'origin_type' => $validated['origin_type'],
                'destination_id' => $validated['destination_id'],
                'destination_type' => $validated['destination_type'],
            ];

            if (!empty($validated['return_date'])) {
                $params['return_date'] = $validated['return_date'];
            }

            $redirectUrl = route('client.routes.show', $params);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'redirect_url' => $redirectUrl]);
            }

            return redirect()->to($redirectUrl);
        } catch (\Throwable $exception) {
            Log::error('Client route search failed', ['error' => $exception->getMessage()]);
            return $this->searchErrorResponse($request, __('client.route_show.search.system_error'));
        }
    }

    public function show(string $slug, Request $request)
    {
        $departureDate = $this->parseDepartureDate($request->input('departure_date'));

        $route = DB::table('routes as r')
            ->join('provinces as ps', 'r.province_start_id', '=', 'ps.id')
            ->join('provinces as pe', 'r.province_end_id', '=', 'pe.id')
            ->select([
                'r.id',
                'r.name',
                'r.slug',
                'r.title',
                'r.description',
                'r.duration',
                'r.distance_km',
                'r.thumbnail_url',
                'r.image_list_url',
                'r.content',
                'r.province_start_id',
                'r.province_end_id',
                'ps.name as start_province_name',
                'ps.slug as start_province_slug',
                'pe.name as end_province_name',
                'pe.slug as end_province_slug',
                DB::raw('COALESCE((SELECT MIN(br.price)
                    FROM bus_routes br
                    JOIN company_routes cr2 ON br.company_route_id = cr2.id
                    WHERE cr2.route_id = r.id AND br.price > 0), 0) as min_price'),
                DB::raw('(SELECT COUNT(*) FROM company_routes cr_count WHERE cr_count.route_id = r.id) as company_count'),
            ])
            ->where('r.slug', $slug)
            ->firstOrFail();

        $companyRoutes = DB::table('company_routes as cr')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->select([
                'cr.id',
                'cr.name as company_route_name',
                'cr.slug',
                'cr.duration',
                'cr.distance_km',
                'cr.thumbnail_url',
                'cr.description',
                'c.name as company_name',
                'c.slug as company_slug',
                'c.thumbnail_url as company_thumbnail',
                DB::raw('(SELECT MIN(br.price) FROM bus_routes br WHERE br.company_route_id = cr.id AND br.price > 0) as min_price'),
                DB::raw("(SELECT s.name FROM company_route_stops crs JOIN stops s ON s.id = crs.stop_id WHERE crs.company_route_id = cr.id AND crs.stop_type IN ('pickup','both') ORDER BY crs.priority ASC LIMIT 1) as pickup_stop_name"),
                DB::raw("(SELECT s.name FROM company_route_stops crs JOIN stops s ON s.id = crs.stop_id WHERE crs.company_route_id = cr.id AND crs.stop_type IN ('dropoff','both') ORDER BY crs.priority ASC LIMIT 1) as dropoff_stop_name"),
                DB::raw('(SELECT br.id FROM bus_routes br WHERE br.company_route_id = cr.id ORDER BY br.start_time ASC LIMIT 1) as next_bus_route_id'),
            ])
            ->where('cr.route_id', $route->id)
            ->orderByDesc('cr.priority')
            ->get();

        $allTrips = $this->loadTripsForRoute($route->id, $departureDate);

        $filterState = $this->buildFilterState($request);
        $filteredTrips = $this->applyTripFilters($allTrips, $filterState);
        $trips = $this->sortTrips($filteredTrips, $filterState['sort']);
        $filterOptions = $this->buildFilterOptions($allTrips);
        $tripStats = [
            'total' => $allTrips->count(),
            'filtered' => $trips->count(),
        ];
        $activeFilterCount = $this->countActiveFilters($filterState);

        $galleryImages = $route->image_list_url ? json_decode($route->image_list_url, true) : [];

        $relatedRoutes = DB::table('routes as related')
            ->select([
                'related.id',
                'related.name',
                'related.slug',
                'related.duration',
                'related.thumbnail_url',
                DB::raw('COALESCE((SELECT MIN(br.price)
                    FROM bus_routes br
                    JOIN company_routes cr2 ON br.company_route_id = cr2.id
                    WHERE cr2.route_id = related.id AND br.price > 0), 0) as min_price'),
            ])
            ->where('related.id', '<>', $route->id)
            ->orderByDesc('related.priority')
            ->limit(6)
            ->get();

        $travelTips = $this->travelTips();
        $faqs = $this->frequentlyAskedQuestions();

        $searchDefaults = [
            'origin' => [
                'id' => $route->province_start_id,
                'type' => 'province',
                'name' => $route->start_province_name,
            ],
            'destination' => [
                'id' => $route->province_end_id,
                'type' => 'province',
                'name' => $route->end_province_name,
            ],
            'departure_date' => $departureDate->format('d/m/Y'),
        ];

        return view('client.routes.show', [
            'route' => $route,
            'companyRoutes' => $companyRoutes,
            'trips' => $trips,
            'filters' => $filterOptions,
            'filterState' => $filterState,
            'tripStats' => $tripStats,
            'activeFilterCount' => $activeFilterCount,
            'galleryImages' => $galleryImages,
            'relatedRoutes' => $relatedRoutes,
            'travelTips' => $travelTips,
            'faqs' => $faqs,
            'departureDate' => $departureDate->format('d/m/Y'),
            'searchDefaults' => $searchDefaults,
            'searchData' => SearchDataBuilder::make(['defaults' => $searchDefaults]),
            'title' => $route->title ?: __('client.route_show.meta_title_dynamic', ['name' => $route->name]),
            'description' => $route->description ?: __('client.route_show.meta_description_dynamic', ['name' => $route->name]),
            'hasActiveFilters' => $activeFilterCount > 0,
        ]);
    }

    private function loadTripsForRoute(int $routeId, Carbon $departureDate)
    {
        $trips = DB::table('bus_routes as br')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('companies as c', 'cr.company_id', '=', 'c.id')
            ->join('buses as b', 'br.bus_id', '=', 'b.id')
            ->select([
                'br.id as bus_route_id',
                'br.start_time',
                'br.end_time',
                'br.price',
                'br.is_active',
                'cr.id as company_route_id',
                'cr.name as company_route_name',
                'cr.slug as company_route_slug',
                'c.name as company_name',
                'c.slug as company_slug',
                'c.thumbnail_url as company_thumbnail',
                'b.name as bus_name',
                'b.model_name as bus_model',
                'b.seat_count',
                'b.seat_map',
                'b.services',
                'b.thumbnail_url as bus_thumbnail',
                'b.image_list_url',
            ])
            ->where('cr.route_id', $routeId)
            ->where('br.is_active', true)
            ->orderBy('br.start_time')
            ->get();

        if ($trips->isEmpty()) {
            return $trips;
        }

        $companyRouteIds = $trips->pluck('company_route_id')->unique()->all();
        $busRouteIds = $trips->pluck('bus_route_id')->unique()->all();

        $allStops = DB::table('company_route_stops as crs')
            ->join('stops as s', 'crs.stop_id', '=', 's.id')
            ->join('districts as d', 's.district_id', '=', 'd.id')
            ->join('provinces as p', 'd.province_id', '=', 'p.id')
            ->select([
                'crs.company_route_id',
                's.id',
                's.name',
                's.address',
                'crs.stop_type',
                'p.name as province_name',
                'd.name as district_name',
            ])
            ->whereIn('crs.company_route_id', $companyRouteIds)
            ->orderBy('crs.priority')
            ->get()
            ->groupBy('company_route_id');

        $allBookedQuantities = DB::table('bookings')
            ->select('bus_route_id', DB::raw('SUM(quantity) as total_quantity'))
            ->whereIn('bus_route_id', $busRouteIds)
            ->whereDate('booking_date', $departureDate)
            ->whereIn('status', ['pending', 'confirmed', 'completed'])
            ->groupBy('bus_route_id')
            ->pluck('total_quantity', 'bus_route_id');

        return $trips->map(function ($trip) use ($allStops, $allBookedQuantities) {
            $services = $trip->services ? json_decode($trip->services, true) : [];
            $images = $trip->image_list_url ? json_decode($trip->image_list_url, true) : [];

            $tripStops = $allStops->get($trip->company_route_id, collect());
            $bookedQuantity = (int)$allBookedQuantities->get($trip->bus_route_id, 0);

            $seatCount = (int)$trip->seat_count;
            $availableSeats = max($seatCount - $bookedQuantity, 0);
            $occupancy = $seatCount > 0 ? round(($bookedQuantity / $seatCount) * 100) : 0;

            $trip->services = $services;
            $trip->bus_images = $images;
            $trip->pickup_points = $tripStops->whereIn('stop_type', ['pickup', 'both'])->values();
            $trip->dropoff_points = $tripStops->whereIn('stop_type', ['dropoff', 'both'])->values();
            $trip->booked_quantity = $bookedQuantity;
            $trip->seats_available = $availableSeats;
            $trip->occupancy_percent = $occupancy;
            $trip->bus_category = $this->determineBusCategory($trip->bus_model, $trip->bus_name, (int)$trip->seat_count);
            $trip->primary_bus_image = $this->resolvePrimaryBusImage($images, $trip->bus_thumbnail);
            $trip->image_gallery = collect($images)->filter()->values()->all();
            $trip->departure_hour = Carbon::createFromFormat('H:i:s', $trip->start_time)->hour;
            $trip->duration_minutes = $this->calculateTripDurationMinutes($trip->start_time, $trip->end_time);
            $trip->price_value = $trip->price && $trip->price > 0 ? (int)$trip->price : null;
            $trip->has_price = $trip->price && $trip->price > 0;
            $trip->seat_capacity = $seatCount;
            $trip->booked_seats = [];

            return $trip;
        });
    }

    private function buildFilterState(Request $request): array
    {
        $sortOptions = ['recommended', 'earliest', 'latest', 'price_low', 'price_high', 'seats_available'];
        $sort = $request->input('sort', 'recommended');
        if (!in_array($sort, $sortOptions, true)) {
            $sort = 'recommended';
        }

        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        $timeRangeKeys = array_keys($this->availableTimeRanges());

        return [
            'sort' => $sort,
            'price_min' => is_numeric($priceMin) ? (int)$priceMin : null,
            'price_max' => is_numeric($priceMax) ? (int)$priceMax : null,
            'services' => $this->normalizeArrayInput($request->input('services')),
            'pickup_points' => $this->normalizeArrayInput($request->input('pickup_points')),
            'dropoff_points' => $this->normalizeArrayInput($request->input('dropoff_points')),
            'bus_categories' => $this->normalizeArrayInput($request->input('bus_categories')),
            'time_ranges' => collect($this->normalizeArrayInput($request->input('time_ranges')))
                ->filter(fn($value) => in_array($value, $timeRangeKeys, true))
                ->values()
                ->all(),
        ];
    }

    private function applyTripFilters(Collection $trips, array $filters): Collection
    {
        $timeRanges = $this->availableTimeRanges();

        return $trips->filter(function ($trip) use ($filters, $timeRanges) {
            $price = $trip->price_value ?? 0;

            if (!is_null($filters['price_min']) && $price < $filters['price_min']) {
                return false;
            }

            if (!is_null($filters['price_max']) && $price > $filters['price_max']) {
                return false;
            }

            if (!empty($filters['services'])) {
                $tripServices = collect($trip->services ?? [])->map(fn($service) => Str::lower($service));
                $requiredServices = collect($filters['services'])->map(fn($service) => Str::lower($service));

                if ($requiredServices->diff($tripServices)->isNotEmpty()) {
                    return false;
                }
            }

            if (!empty($filters['pickup_points'])) {
                $pickupNames = collect($trip->pickup_points ?? [])->pluck('name')->map(fn($value) => Str::lower($value));
                $selectedPickups = collect($filters['pickup_points'])->map(fn($value) => Str::lower($value));

                if (!$selectedPickups->some(fn($value) => $pickupNames->contains($value))) {
                    return false;
                }
            }

            if (!empty($filters['dropoff_points'])) {
                $dropoffNames = collect($trip->dropoff_points ?? [])->pluck('name')->map(fn($value) => Str::lower($value));
                $selectedDropoffs = collect($filters['dropoff_points'])->map(fn($value) => Str::lower($value));

                if (!$selectedDropoffs->some(fn($value) => $dropoffNames->contains($value))) {
                    return false;
                }
            }

            if (!empty($filters['bus_categories']) && !in_array($trip->bus_category, $filters['bus_categories'], true)) {
                return false;
            }

            if (!empty($filters['time_ranges'])) {
                $hour = $trip->departure_hour ?? Carbon::createFromFormat('H:i:s', $trip->start_time)->hour;

                $matchesTimeRange = collect($filters['time_ranges'])->some(function ($key) use ($timeRanges, $hour) {
                    if (!isset($timeRanges[$key])) {
                        return false;
                    }

                    $range = $timeRanges[$key];
                    $start = $range['start'];
                    $end = $range['end'];

                    if ($end <= $start) {
                        $end += 24;
                    }

                    $hourToCompare = $hour;
                    if ($hourToCompare < $start) {
                        $hourToCompare += 24;
                    }

                    return $hourToCompare >= $start && $hourToCompare < $end;
                });

                if (!$matchesTimeRange) {
                    return false;
                }
            }

            return true;
        })->values();
    }

    private function sortTrips(Collection $trips, ?string $sort): Collection
    {
        $sort = $sort ?: 'recommended';

        return $trips->sort(function ($a, $b) use ($sort) {
            switch ($sort) {
                case 'earliest':
                    return strcmp($a->start_time, $b->start_time);
                case 'latest':
                    return strcmp($b->start_time, $a->start_time);
                case 'price_low':
                    $priceA = $a->price_value ?? PHP_INT_MAX;
                    $priceB = $b->price_value ?? PHP_INT_MAX;

                    if ($priceA === $priceB) {
                        return strcmp($a->start_time, $b->start_time);
                    }

                    return $priceA <=> $priceB;
                case 'price_high':
                    $priceA = $a->price_value ?? -1;
                    $priceB = $b->price_value ?? -1;

                    if ($priceA === $priceB) {
                        return strcmp($a->start_time, $b->start_time);
                    }

                    return $priceB <=> $priceA;
                case 'seats_available':
                    if (($a->seats_available ?? 0) === ($b->seats_available ?? 0)) {
                        return strcmp($a->start_time, $b->start_time);
                    }

                    return ($b->seats_available ?? 0) <=> ($a->seats_available ?? 0);
                default:
                    $hasPriceA = $a->has_price ?? false;
                    $hasPriceB = $b->has_price ?? false;

                    if ($hasPriceA !== $hasPriceB) {
                        return $hasPriceA ? -1 : 1;
                    }

                    if (($a->seats_available ?? 0) !== ($b->seats_available ?? 0)) {
                        return ($b->seats_available ?? 0) <=> ($a->seats_available ?? 0);
                    }

                    if ($hasPriceA && ($a->price_value ?? 0) !== ($b->price_value ?? 0)) {
                        return ($a->price_value ?? 0) <=> ($b->price_value ?? 0);
                    }

                    return strcmp($a->start_time, $b->start_time);
            }
        })->values();
    }

    private function buildFilterOptions(Collection $trips): array
    {
        $prices = $trips->pluck('price_value')->filter(fn($price) => $price && $price > 0);

        return [
            'price' => [
                'min' => $prices->min() ? (int)$prices->min() : 0,
                'max' => $prices->max() ? (int)$prices->max() : 0,
            ],
            'services' => $trips->flatMap(fn($trip) => collect($trip->services ?? []))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all(),
            'pickup_points' => $trips->flatMap(fn($trip) => collect($trip->pickup_points ?? [])->pluck('name'))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all(),
            'dropoff_points' => $trips->flatMap(fn($trip) => collect($trip->dropoff_points ?? [])->pluck('name'))
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all(),
            'bus_categories' => $trips->pluck('bus_category')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all(),
            'time_ranges' => $this->availableTimeRanges(),
        ];
    }

    private function availableTimeRanges(): array
    {
        return [
            'early_morning' => [
                'label' => __('client.route_show.filters.time_range_early_morning'),
                'start' => 0,
                'end' => 6,
            ],
            'morning' => [
                'label' => __('client.route_show.filters.time_range_morning'),
                'start' => 6,
                'end' => 12,
            ],
            'afternoon' => [
                'label' => __('client.route_show.filters.time_range_afternoon'),
                'start' => 12,
                'end' => 18,
            ],
            'evening' => [
                'label' => __('client.route_show.filters.time_range_evening'),
                'start' => 18,
                'end' => 24,
            ],
        ];
    }

    private function normalizeArrayInput($value): array
    {
        if ($value instanceof Collection) {
            $value = $value->all();
        }

        return collect(Arr::wrap($value))
            ->map(fn($item) => trim((string)$item))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    private function determineBusCategory(?string $busModel, ?string $busName, int $seatCount): string
    {
        $keywords = Str::lower(trim(($busModel ?? '') . ' ' . ($busName ?? '')));

        if (Str::contains($keywords, ['limousine', 'limo'])) {
            return __('client.route_show.bus_categories.limousine');
        }

        if (Str::contains($keywords, ['cabin', 'suite'])) {
            return __('client.route_show.bus_categories.cabin');
        }

        if (Str::contains($keywords, ['giường', 'giuong', 'sleep'])) {
            return __('client.route_show.bus_categories.sleeper');
        }

        if (Str::contains($keywords, ['ghế', 'ghe', 'seat'])) {
            return __('client.route_show.bus_categories.seat');
        }

        if ($seatCount >= 32) {
            return __('client.route_show.bus_categories.sleeper');
        }

        if ($seatCount >= 16) {
            return __('client.route_show.bus_categories.seat');
        }

        return __('client.route_show.bus_categories.other');
    }

    private function resolvePrimaryBusImage(?array $images, ?string $fallback): string
    {
        $primary = collect($images ?? [])->filter()->first();

        if ($primary) {
            return $primary;
        }

        if ($fallback) {
            return $fallback;
        }

        return '/userfiles/files/king/cabin/1.jpg';
    }

    private function calculateTripDurationMinutes(?string $startTime, ?string $endTime): int
    {
        if (!$startTime || !$endTime) {
            return 0;
        }

        try {
            $start = Carbon::createFromFormat('H:i:s', $startTime);
            $end = Carbon::createFromFormat('H:i:s', $endTime);

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            return (int)$start->diffInMinutes($end);
        } catch (\Throwable $exception) {
            return 0;
        }
    }

    private function countActiveFilters(array $filterState): int
    {
        return collect($filterState)
            ->reject(function ($value, $key) {
                if ($key === 'sort') {
                    return true;
                }

                if (is_array($value)) {
                    return empty($value);
                }

                return is_null($value);
            })
            ->count();
    }

    private function parseDepartureDate(?string $value): Carbon
    {
        if ($value) {
            try {
                return Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
            } catch (\Throwable $exception) {
                // Bỏ qua và sử dụng giá trị mặc định
            }
        }

        return Carbon::today();
    }

    private function resolveProvinceId(string $type, int $id): ?int
    {
        if ($type === 'province') {
            return $id;
        }

        if ($type === 'district') {
            return DB::table('districts')->where('id', $id)->value('province_id');
        }

        if ($type === 'stop') {
            return DB::table('stops as s')
                ->join('districts as d', 's.district_id', '=', 'd.id')
                ->where('s.id', $id)
                ->value('d.province_id');
        }

        return null;
    }

    private function searchErrorResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 404);
        }

        return redirect()->back()->withInput($request->all())->with('error', $message);
    }

    private function travelTips(): array
    {
        return [
            [
                'icon' => 'fa-solid fa-clock',
                'title' => __('client.route_show.tips.tip_1_title'),
                'content' => __('client.route_show.tips.tip_1_content'),
            ],
            [
                'icon' => 'fa-solid fa-suitcase-rolling',
                'title' => __('client.route_show.tips.tip_2_title'),
                'content' => __('client.route_show.tips.tip_2_content'),
            ],
            [
                'icon' => 'fa-solid fa-mug-hot',
                'title' => __('client.route_show.tips.tip_3_title'),
                'content' => __('client.route_show.tips.tip_3_content'),
            ],
        ];
    }

    private function frequentlyAskedQuestions(): array
    {
        return [
            [
                'question' => __('client.route_show.faq.faq_1_question'),
                'answer' => __('client.route_show.faq.faq_1_answer'),
            ],
            [
                'question' => __('client.route_show.faq.faq_2_question'),
                'answer' => __('client.route_show.faq.faq_2_answer'),
            ],
            [
                'question' => __('client.route_show.faq.faq_3_question'),
                'answer' => __('client.route_show.faq.faq_3_answer'),
            ],
        ];
    }
}
