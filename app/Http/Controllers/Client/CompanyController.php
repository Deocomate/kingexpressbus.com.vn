<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Support\Client\SearchDataBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = trim((string) $request->input('q', ''));

        $companiesQuery = DB::table('companies as c')
            ->select([
                'c.id',
                'c.name',
                'c.slug',
                'c.thumbnail_url',
                'c.description',
                'c.address',
                'c.phone',
                'c.email',
                'c.hotline',
                'c.priority',
            ])
            ->orderByDesc('c.priority')
            ->orderBy('c.name');

        if ($searchTerm !== '') {
            $companiesQuery->where(function ($query) use ($searchTerm) {
                $like = '%' . $searchTerm . '%';
                $query->where('c.name', 'like', $like)
                    ->orWhere('c.slug', 'like', $like)
                    ->orWhere('c.address', 'like', $like);
            });
        }

        $companiesQuery->addSelect([
            DB::raw('(SELECT COUNT(DISTINCT cr.id) FROM company_routes cr WHERE cr.company_id = c.id) as route_count'),
            DB::raw('(SELECT COUNT(DISTINCT br.id) FROM bus_routes br JOIN company_routes cr2 ON cr2.id = br.company_route_id WHERE cr2.company_id = c.id AND br.is_active = 1) as active_trip_count'),
            DB::raw('(SELECT MIN(br.price) FROM bus_routes br JOIN company_routes cr3 ON cr3.id = br.company_route_id WHERE cr3.company_id = c.id AND br.price > 0) as min_price'),
        ]);

        $companies = $companiesQuery->paginate(9)->withQueryString();

        $featuredRoutes = DB::table('routes as r')
            ->join('company_routes as cr', 'cr.route_id', '=', 'r.id')
            ->select([
                'r.id',
                'r.name',
                'r.slug',
                'r.thumbnail_url',
                DB::raw('MIN(br.price) as min_price'),
            ])
            ->leftJoin('bus_routes as br', 'br.company_route_id', '=', 'cr.id')
            ->groupBy('r.id', 'r.name', 'r.slug', 'r.thumbnail_url')
            ->orderByDesc('r.priority')
            ->limit(6)
            ->get();

        return view('client.companies.index', [
            'companies' => $companies,
            'featuredRoutes' => $featuredRoutes,
            'filters' => [
                'search' => $searchTerm,
            ],
            'title' => 'Danh sách nhà xe đối tác',
            'description' => 'Tổng hợp các nhà xe đang hợp tác cùng King Express Bus, thông tin tuyến và giá vé tối thiểu.',
            'searchData' => SearchDataBuilder::make(),
        ]);
    }

    public function show(string $slug)
    {
        $company = DB::table('companies as c')
            ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
            ->select([
                'c.id',
                'c.name',
                'c.slug',
                'c.thumbnail_url',
                'c.description',
                'c.address',
                'c.phone',
                'c.email',
                'c.hotline',
                'c.content',
                'c.priority',
                'u.email as account_email',
            ])
            ->where('c.slug', $slug)
            ->first();

        abort_if(!$company, 404);

        $routes = DB::table('company_routes as cr')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->select([
                'cr.id',
                'cr.name',
                'cr.slug',
                'cr.duration',
                'cr.distance_km',
                'cr.thumbnail_url',
                'cr.description',
                'r.slug as route_slug',
                'r.name as route_name',
                DB::raw('(SELECT MIN(br.price) FROM bus_routes br WHERE br.company_route_id = cr.id AND br.price > 0) as min_price'),
            ])
            ->where('cr.company_id', $company->id)
            ->orderByDesc('cr.priority')
            ->get();

        $busFleet = DB::table('buses as b')
            ->select([
                'b.id',
                'b.name',
                'b.model_name',
                'b.seat_count',
                'b.thumbnail_url',
                'b.image_list_url',
                'b.services',
                'b.content',
                'b.priority',
            ])
            ->where('b.company_id', $company->id)
            ->orderByDesc('b.priority')
            ->get()
            ->map(function ($bus) {
                $bus->services = $bus->services ? json_decode($bus->services, true) : [];
                $bus->images = $bus->image_list_url ? json_decode($bus->image_list_url, true) : [];
                return $bus;
            });

        $upcomingTrips = DB::table('bus_routes as br')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->join('routes as r', 'cr.route_id', '=', 'r.id')
            ->select([
                'br.id',
                'br.start_time',
                'br.end_time',
                'br.price',
                'cr.slug as company_route_slug',
                'cr.name as company_route_name',
                'r.name as route_name',
                'r.slug as route_slug',
            ])
            ->where('cr.company_id', $company->id)
            ->where('br.is_active', true)
            ->orderBy('br.start_time')
            ->limit(12)
            ->get();

        $minPrice = DB::table('bus_routes as br')
            ->join('company_routes as cr', 'br.company_route_id', '=', 'cr.id')
            ->where('cr.company_id', $company->id)
            ->where('br.price', '>', 0)
            ->min('br.price');

        $statistics = [
            'route_count' => $routes->count(),
            'fleet_size' => $busFleet->count(),
            'active_trip_count' => $upcomingTrips->count(),
            'min_price' => $minPrice,
        ];

        return view('client.companies.show', [
            'company' => $company,
            'routes' => $routes,
            'busFleet' => $busFleet,
            'upcomingTrips' => $upcomingTrips,
            'statistics' => $statistics,
            'title' => 'Nhà xe ' . $company->name,
            'description' => $company->description ?: 'Thông tin chi tiết về nhà xe ' . $company->name . ' của King Express Bus.',
            'searchData' => SearchDataBuilder::make(),
        ]);
    }
}
