<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Support\Client\SearchDataBuilder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        $searchData = SearchDataBuilder::make();

        $popularRoutes = DB::table('routes as r')
            ->select([
                'r.id',
                'r.name',
                'r.slug',
                'r.description',
                'r.duration',
                'r.distance_km',
                'r.thumbnail_url',
                DB::raw('(SELECT MIN(br.price) FROM bus_routes br JOIN company_routes cr ON cr.id = br.company_route_id WHERE cr.route_id = r.id AND br.price > 0) as min_price'),
                DB::raw('(SELECT COUNT(DISTINCT cr.id) FROM company_routes cr WHERE cr.route_id = r.id) as company_count'),
            ])
            ->orderByDesc('r.priority')
            ->limit(8)
            ->get();

        $featuredCompanies = DB::table('companies as c')
            ->select([
                'c.id',
                'c.name',
                'c.slug',
                'c.thumbnail_url',
                'c.description',
                DB::raw('(SELECT COUNT(DISTINCT cr.id) FROM company_routes cr WHERE cr.company_id = c.id) as route_count'),
            ])
            ->orderByDesc('c.priority')
            ->limit(8)
            ->get();

        $busHighlights = DB::table('buses as b')
            ->select([
                'b.id',
                'b.name',
                'b.model_name',
                'b.thumbnail_url',
                'b.services',
                'b.content',
            ])
            ->orderByDesc('b.priority')
            ->limit(4)
            ->get()
            ->map(function ($bus) {
                $bus->services = $bus->services ? json_decode($bus->services, true) : [];
                return $bus;
            });

        $stats = [
            'route_count' => DB::table('routes')->count(),
            'company_count' => DB::table('companies')->count(),
            'bus_count' => DB::table('buses')->count(),
            'customer_count' => DB::table('users')->where('role', 'customer')->count(),
        ];
        $serviceHighlights = $this->serviceHighlights();
        $heroSlides = $this->heroSlides();
        $mediaShowcase = $this->mediaShowcase();
        $galleryImages = $this->galleryImages();
        $testimonials = $this->testimonials();
        $partnerLogos = $this->partnerLogos();

        return view('client.home.index', compact(
            'searchData',
            'popularRoutes',
            'featuredCompanies',
            'busHighlights',
            'stats',
            'serviceHighlights',
            'heroSlides',
            'mediaShowcase',
            'galleryImages',
            'testimonials',
            'partnerLogos'
        ));
    }

    private function serviceHighlights(): array
    {
        if (!Schema::hasTable('bus_services')) {
            return $this->defaultServiceHighlights();
        }

        $services = DB::table('bus_services')
            ->orderByDesc(DB::raw('priority'))
            ->orderBy('name')
            ->limit(6)
            ->get();

        if ($services->isEmpty()) {
            return $this->defaultServiceHighlights();
        }

        return $services->map(function ($service) {
            return [
                'title' => $service->name,
                'description' => 'Tiện ích tiêu chuẩn trên toàn bộ xe King Express Bus.',
                'icon' => $service->icon ?: 'fa-solid fa-circle-check',
                'image' => '/userfiles/files/kingexpressbus/cabin/2.jpg',
            ];
        })->toArray();
    }

    private function defaultServiceHighlights(): array
    {
        return [
            [
                'title' => 'Cabin riêng tư',
                'description' => 'Không gian cabin kín đáo, khởi động êm và đầy đủ tiện nghi.',
                'icon' => 'fa-solid fa-bed',
                'image' => '/userfiles/files/kingexpressbus/cabin/4.jpg',
            ],
            [
                'title' => 'Tiện ích thông minh',
                'description' => 'Chăn ấm, sạc điện, TV, điều hòa tự động trên mỗi chuyến.',
                'icon' => 'fa-solid fa-plug',
                'image' => '/userfiles/files/kingexpressbus/sleeper/2.jpg',
            ],
            [
                'title' => 'Hỗ trợ 24/7',
                'description' => 'Tổng đài luôn sẵn sàng đồng hành trong mọi hành trình.',
                'icon' => 'fa-solid fa-headset',
                'image' => '/userfiles/files/kingexpressbus/sleeper/5.jpg',
            ],
        ];
    }

    private function heroSlides(): array
    {
        $today = now()->format('d/m/Y');

        $routeHaNoiSapa = DB::table('routes')->where('slug', 'ha-noi-sapa')->first();
        $routeHueHoiAn = DB::table('routes')->where('slug', 'hue-hoi-an')->first();

        return array_filter([
            [
                'title' => 'Hành trình cao cấp Hà Nội - Sa Pa',
                'subtitle' => 'Cabin riêng tư, khởi hành mỗi ngày, trả tận nơi trung tâm Sa Pa.',
                'image' => '/userfiles/files/city_imgs/lao-cai-sa-pa.jpg',
                'cta_text' => 'Đặt vé ngay',
                'cta_url' => $routeHaNoiSapa ? route('client.routes.show', ['slug' => $routeHaNoiSapa->slug, 'departure_date' => $today]) : route('client.routes.search'),
            ],
            [
                'title' => 'Trải nghiệm Ninh Bình trở nên dễ dàng',
                'subtitle' => 'Phù hợp tham quan Tam Cốc, Tràng An, thời gian linh hoạt.',
                'image' => '/userfiles/files/city_imgs/ninh-binh.jpg',
                'cta_text' => 'Xem lịch chạy',
                'cta_url' => route('client.routes.search'),
            ],
            [
                'title' => 'Huế - Hội An sang trọng',
                'subtitle' => 'Xe limousine 9 chỗ và giường nằm giúp bạn nghỉ ngơi thoải mái.',
                'image' => '/userfiles/files/city_imgs/hoi-an.jpg',
                'cta_text' => 'Khám phá ngay',
                'cta_url' => $routeHueHoiAn ? route('client.routes.show', ['slug' => $routeHueHoiAn->slug, 'departure_date' => $today]) : route('client.routes.search'),
            ],
        ]);
    }

    private function mediaShowcase(): array
    {
        return [
            [
                'type' => 'image',
                'title' => 'Không gian cabin limousine',
                'description' => 'Cabin riêng tư, rèm che và ghế nâng cấp.',
                'asset' => '/userfiles/files/kingexpressbus/cabin/5.jpg',
            ],
            [
                'type' => 'image',
                'title' => 'Giường nằm thông minh',
                'description' => 'Sắp xếp hợp lý, lên xuống an toàn, phù hợp du lịch đêm.',
                'asset' => '/userfiles/files/kingexpressbus/sleeper/6.jpg',
            ],
            [
                'type' => 'image',
                'title' => 'Bến xe trung tâm',
                'description' => 'Điểm đón trả nằm ngay trung tâm thành phố, di chuyển thuận tiện.',
                'asset' => '/userfiles/files/city_imgs/ha-noi.jpg',
            ],
        ];
    }

    private function galleryImages(): array
    {
        return [
            [
                'title' => 'Phòng chờ khách sang trọng',
                'url' => '/userfiles/files/kingexpressbus/sleeper/8.jpg',
            ],
            [
                'title' => 'Quảng cảnh Hội An',
                'url' => '/userfiles/files/city_imgs/hoi-an.jpg',
            ],
            [
                'title' => 'Ninh Bình trong xanh',
                'url' => '/userfiles/files/city_imgs/ninh-binh.jpg',
            ],
            [
                'title' => 'Hà Nội về đêm',
                'url' => '/userfiles/files/city_imgs/ha-noi.jpg',
            ],
        ];
    }

    private function testimonials(): array
    {
        return [
            [
                'name' => 'Nguyễn Minh Anh',
                'route' => 'Hà Nội - Sa Pa',
                'avatar' => null,
                'quote' => 'Lịch trình rõ ràng, nhân viên hỗ trợ nhiệt tình và chu đáo.',
            ],
            [
                'name' => 'Trần Quang Huy',
                'route' => 'Huế - Hội An',
                'avatar' => null,
                'quote' => 'Xe đẹp, sạch sẽ, giường nằm thoải mái. Tôi sẽ giới thiệu cho bạn bè.',
            ],
            [
                'name' => 'Lê Thu Hiền',
                'route' => 'Hà Nội - Ninh Bình',
                'avatar' => null,
                'quote' => 'Phù hợp cho gia đình, đường đi an toàn, đón trả đúng điểm hẹn.',
            ],
        ];
    }

    private function partnerLogos(): array
    {
        return [
            '/userfiles/files/web information/logo.jpg',
            '/userfiles/files/kingexpressbus/sleeper/10.jpg',
            '/userfiles/files/kingexpressbus/cabin/2.jpg',
            '/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-2.jpg',
        ];
    }
}
