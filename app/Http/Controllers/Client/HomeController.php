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
                'description' => __('client.home.service_highlights.item_description_default'),
                'icon' => $service->icon ?: 'fa-solid fa-circle-check',
                'image' => '/userfiles/files/kingexpressbus/cabin/2.jpg',
            ];
        })->toArray();
    }

    private function defaultServiceHighlights(): array
    {
        return [
            [
                'title' => __('client.home.default_services.item_1_title'),
                'description' => __('client.home.default_services.item_1_description'),
                'icon' => 'fa-solid fa-bed',
                'image' => '/userfiles/files/kingexpressbus/cabin/4.jpg',
            ],
            [
                'title' => __('client.home.default_services.item_2_title'),
                'description' => __('client.home.default_services.item_2_description'),
                'icon' => 'fa-solid fa-plug',
                'image' => '/userfiles/files/kingexpressbus/sleeper/2.jpg',
            ],
            [
                'title' => __('client.home.default_services.item_3_title'),
                'description' => __('client.home.default_services.item_3_description'),
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
                'title' => __('client.home.hero.slide_1_title'),
                'subtitle' => __('client.home.hero.slide_1_subtitle'),
                'image' => '/userfiles/files/city_imgs/lao-cai-sa-pa.jpg',
                'cta_text' => __('client.home.hero.slide_1_cta'),
                'cta_url' => $routeHaNoiSapa ? route('client.routes.show', ['slug' => $routeHaNoiSapa->slug, 'departure_date' => $today]) : route('client.routes.search'),
            ],
            [
                'title' => __('client.home.hero.slide_2_title'),
                'subtitle' => __('client.home.hero.slide_2_subtitle'),
                'image' => '/userfiles/files/city_imgs/ninh-binh.jpg',
                'cta_text' => __('client.home.hero.slide_2_cta'),
                'cta_url' => route('client.routes.search'),
            ],
            [
                'title' => __('client.home.hero.slide_3_title'),
                'subtitle' => __('client.home.hero.slide_3_subtitle'),
                'image' => '/userfiles/files/city_imgs/hoi-an.jpg',
                'cta_text' => __('client.home.hero.slide_3_cta'),
                'cta_url' => $routeHueHoiAn ? route('client.routes.show', ['slug' => $routeHueHoiAn->slug, 'departure_date' => $today]) : route('client.routes.search'),
            ],
        ]);
    }

    private function mediaShowcase(): array
    {
        return [
            [
                'type' => 'image',
                'title' => __('client.home.media_showcase.item_1_title'),
                'description' => __('client.home.media_showcase.item_1_description'),
                'asset' => '/userfiles/files/kingexpressbus/cabin/5.jpg',
            ],
            [
                'type' => 'image',
                'title' => __('client.home.media_showcase.item_2_title'),
                'description' => __('client.home.media_showcase.item_2_description'),
                'asset' => '/userfiles/files/kingexpressbus/sleeper/6.jpg',
            ],
            [
                'type' => 'image',
                'title' => __('client.home.media_showcase.item_3_title'),
                'description' => __('client.home.media_showcase.item_3_description'),
                'asset' => '/userfiles/files/city_imgs/ha-noi.jpg',
            ],
        ];
    }

    private function galleryImages(): array
    {
        return [
            [
                'title' => __('client.home.gallery.item_1_title'),
                'url' => '/userfiles/files/kingexpressbus/sleeper/8.jpg',
            ],
            [
                'title' => __('client.home.gallery.item_2_title'),
                'url' => '/userfiles/files/city_imgs/hoi-an.jpg',
            ],
            [
                'title' => __('client.home.gallery.item_3_title'),
                'url' => '/userfiles/files/city_imgs/ninh-binh.jpg',
            ],
            [
                'title' => __('client.home.gallery.item_4_title'),
                'url' => '/userfiles/files/city_imgs/ha-noi.jpg',
            ],
        ];
    }

    private function testimonials(): array
    {
        return [
            [
                'name' => 'Nguyễn Minh Anh',
                'route' => __('client.home.testimonials.item_1_route'),
                'avatar' => null,
                'quote' => __('client.home.testimonials.item_1_quote'),
            ],
            [
                'name' => 'Trần Quang Huy',
                'route' => __('client.home.testimonials.item_2_route'),
                'avatar' => null,
                'quote' => __('client.home.testimonials.item_2_quote'),
            ],
            [
                'name' => 'Lê Thu Hiền',
                'route' => __('client.home.testimonials.item_3_route'),
                'avatar' => null,
                'quote' => __('client.home.testimonials.item_3_quote'),
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
