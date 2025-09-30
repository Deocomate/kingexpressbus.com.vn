<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->truncateTables();

        $this->call(UserSeeder::class);
        $this->seedWebProfiles();
        $this->seedDistrictTypes();
        $provinces = $this->seedProvinces();
        $districts = $this->seedDistricts($provinces);
        $stops = $this->seedStops($districts);
        $companies = $this->seedCompanies();
        $buses = $this->seedBuses($companies);
        $routes = $this->seedRoutes($provinces);
        $companyRoutes = $this->seedCompanyRoutes($routes, $companies);
        $this->seedCompanyRouteStops($companyRoutes, $routes, $stops);
        $busRoutes = $this->seedBusRoutes($buses, $companyRoutes, $routes);
        $this->seedMenus($routes);
        $this->seedBookings($busRoutes);


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    private function truncateTables(): void
    {
        $tables = [
            'users',
            'web_profiles',
            'menus',
            'provinces',
            'district_types',
            'districts',
            'stops',
            'routes',
            'companies',
            'buses',
            'company_routes',
            'company_route_stops',
            'bus_routes',
            'bookings'
        ];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function seedWebProfiles(): void
    {
        DB::table('web_profiles')->insert([
            [
                'id' => 1,
                'profile_name' => 'Cấu hình mặc định',
                'is_default' => true,
                'title' => 'King Express Bus - Nhà xe chất lượng cao',
                'description' => 'Chuyên cung cấp dịch vụ vận tải hành khách tuyến Bắc - Nam với dòng xe limousine và giường nằm cao cấp. An toàn, tiện nghi và đúng giờ.',
                'logo_url' => '/userfiles/files/web%20information/logo.jpg',
                'favicon_url' => null,
                'email' => 'kingexpressbus@gmail.com',
                'phone' => '0865095066',
                'hotline' => '0865095066',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'facebook_url' => 'https://www.facebook.com/HanoiSapa',
                'zalo_url' => 'https://zalo.me/0865095066',
                'map_embedded' => '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.09681418826!2d105.8480952758832!3d21.02882898062132!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab953255153b%3A0x1326315159040a8!2zMTkgUC4gSMàbmcgVGhp4bq_YywgSMàbmcgQsOhLCBIb8OgbiBLaeG6v20sIEjDoCBO4buZaSwgVmnhu4d0IE5hbQ!5e0!3m2!1svi!2s!4v1694982361623!5m2!1svi!2s\" width=\"100%\" height=\"100%\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\" referrerpolicy=\"no-referrer-when-downgrade\"></iframe>',
                'policy_content' => '<h1>Chính sách chung</h1><p>Chính sách và điều khoản của nhà xe King Express Bus</p>',
                'introduction_content' => '<h2>Về chúng tôi</h2><p>King Express Bus tự hào là một trong những đơn vị vận tải hàng đầu</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    private function seedDistrictTypes(): void
    {
        DB::table('district_types')->insert([
            ['id' => 1, 'name' => 'Bến xe', 'priority' => 1],
            ['id' => 2, 'name' => 'Văn phòng', 'priority' => 2],
            ['id' => 3, 'name' => 'Điểm đón trả', 'priority' => 3],
            ['id' => 4, 'name' => 'Quận', 'priority' => 4],
            ['id' => 5, 'name' => 'Xã', 'priority' => 5],
            ['id' => 6, 'name' => 'Thành Phố', 'priority' => 0],
        ]);
    }

    private function seedProvinces(): array
    {
        $data = [
            ['id' => 1, 'name' => 'Hà Nội', 'slug' => 'ha-noi'],
            ['id' => 2, 'name' => 'Sapa', 'slug' => 'sapa'],
            ['id' => 3, 'name' => 'Tuần Châu', 'slug' => 'tuan-chau'],
            ['id' => 4, 'name' => 'Mai Châu', 'slug' => 'mai-chau'],
            ['id' => 5, 'name' => 'Cát Bà', 'slug' => 'cat-ba'],
            ['id' => 6, 'name' => 'Hà Giang', 'slug' => 'ha-giang'],
            ['id' => 7, 'name' => 'Ninh Bình', 'slug' => 'ninh-binh'],
            ['id' => 8, 'name' => 'Phong Nha', 'slug' => 'phong-nha'],
            ['id' => 9, 'name' => 'Huế', 'slug' => 'hue'],
            ['id' => 10, 'name' => 'Đà Nẵng', 'slug' => 'da-nang'],
            ['id' => 11, 'name' => 'Hội An', 'slug' => 'hoi-an'],
        ];

        $insertData = array_map(function ($item, $index) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'title' => 'Vé xe khách đi ' . $item['name'],
                'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi ' . $item['name'] . ' và các tỉnh.',
                'thumbnail_url' => '/userfiles/files/city_imgs/' . $item['slug'] . '.jpg',
                'image_list_url' => json_encode(['/userfiles/files/city_imgs/' . $item['slug'] . '.jpg']),
                'content' => null,
                'priority' => $index + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $data, array_keys($data));

        DB::table('provinces')->insert($insertData);
        return $data;
    }

    private function seedDistricts(array $provinces): array
    {
        $districts = [];
        $id = 1;
        foreach ($provinces as $province) {
            $name = 'Thành Phố ' . $province['name'];
            $districts[] = [
                'id' => $id,
                'province_id' => $province['id'],
                'district_type_id' => 6,
                'name' => $name,
                'slug' => Str::slug($name),
                'title' => $name,
                'description' => 'Các điểm đón trả tại ' . $name,
                'thumbnail_url' => null,
                'image_list_url' => null,
                'content' => null,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $id++;
        }
        DB::table('districts')->insert($districts);
        return $districts;
    }

    private function seedStops(array $districts): array
    {
        $addressMap = [
            1 => '19 Hàng Thiếc',
            2 => '458 Dien Bien Phu',
            3 => 'Tuần Châu Harbor',
            4 => 'Hotel',
            5 => '217 Mot Thang Tu',
            6 => '100 Tran Phu',
            7 => 'No 2a, đường 27/7 - TP Ninh Bình',
            8 => 'Central Backpacker Phong Nha',
            9 => '07 Đội Cung- TP Huế',
            10 => 'Số 28 đường 3/2- TP Đà Nẵng',
            11 => '105 Tôn Đức Thắng - TP Hội An',
            12 => 'Travel Agency- Tam Cốc (NEW PICK UP POINT)',
            13 => 'Đồng Gừng Bus Station'
        ];

        $stopsData = [
            ['id' => 1, 'district_id' => 1, 'name' => 'VP Hà Nội', 'address' => $addressMap[1]],
            ['id' => 2, 'district_id' => 2, 'name' => 'VP Sapa', 'address' => $addressMap[2]],
            ['id' => 3, 'district_id' => 3, 'name' => 'VP Tuần Châu', 'address' => $addressMap[3]],
            ['id' => 4, 'district_id' => 4, 'name' => 'VP Mai Châu', 'address' => $addressMap[4]],
            ['id' => 5, 'district_id' => 5, 'name' => 'VP Cát Bà', 'address' => $addressMap[5]],
            ['id' => 6, 'district_id' => 6, 'name' => 'VP Hà Giang', 'address' => $addressMap[6]],
            ['id' => 7, 'district_id' => 7, 'name' => 'VP Ninh Bình', 'address' => $addressMap[7]],
            ['id' => 8, 'district_id' => 8, 'name' => 'VP Phong Nha', 'address' => $addressMap[8]],
            ['id' => 9, 'district_id' => 9, 'name' => 'VP Huế', 'address' => $addressMap[9]],
            ['id' => 10, 'district_id' => 10, 'name' => 'VP Đà Nẵng', 'address' => $addressMap[10]],
            ['id' => 11, 'district_id' => 11, 'name' => 'VP Hội An', 'address' => $addressMap[11]],
            ['id' => 12, 'district_id' => 7, 'name' => 'VP Tam Cốc', 'address' => $addressMap[12]],
            ['id' => 13, 'district_id' => 7, 'name' => 'BX Đồng Gừng', 'address' => $addressMap[13]],
        ];

        $stops = array_map(function ($stop) {
            $stop['priority'] = 0;
            $stop['created_at'] = now();
            $stop['updated_at'] = now();
            return $stop;
        }, $stopsData);

        DB::table('stops')->insert($stops);
        return $stops;
    }

    private function seedCompanies(): array
    {
        $data = [
            [
                'id' => 1,
                'user_id' => 6,
                'name' => 'King Express Bus',
                'slug' => 'king-express-bus',
                'title' => 'Nhà xe King Express Bus',
                'description' => 'Dịch vụ xe khách chất lượng cao',
                'thumbnail_url' => '/userfiles/files/web%20information/logo.jpg',
                'image_list_url' => null,
                'content' => '<p>Giới thiệu về nhà xe King Express Bus.</p>',
                'phone' => '0865095066',
                'email' => 'kingexpressbus.booking@gmail.com',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];
        DB::table('companies')->insert($data);
        return $data;
    }

    private function seedBuses(array $companies): array
    {
        $companyId = $companies[0]['id'];
        $data = [
            [
                'id' => 1, 'name' => 'Sleeper', 'model_name' => 'Sleeper Bus', 'seat_count' => 38,
                'services' => json_encode(['Chăn gối', 'Nước uống']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/sleeper/1.jpg',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/sleeper/1.jpg', '/userfiles/files/kingexpressbus/sleeper/2.jpg']),
                'content' => '<p>Xe giường nằm tiêu chuẩn là lựa chọn kinh tế cho các chuyến đi dài.</p>', 'priority' => 1,
            ],
            [
                'id' => 2, 'name' => 'Cabin Single', 'model_name' => 'Cabin Single', 'seat_count' => 22,
                'services' => json_encode(['Nước uống', 'Rèm che', 'TV', 'Cổng sạc USB']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin/1.jpg',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/cabin/1.jpg', '/userfiles/files/kingexpressbus/cabin/2.jpg', '/userfiles/files/kingexpressbus/cabin/3.jpg']),
                'content' => '<p>Cabin đơn đem lại sự riêng tư tuyệt đối với đầy đủ tiện nghi.</p>', 'priority' => 3,
            ],
            [
                'id' => 3, 'name' => 'Cabin Double', 'model_name' => 'Cabin Double', 'seat_count' => 22,
                'services' => json_encode(['Chăn gối', 'TV', 'Cổng sạc USB']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin_double/1.jpg',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/cabin_double/1.jpg', '/userfiles/files/kingexpressbus/cabin_double/2.jpg', '/userfiles/files/kingexpressbus/cabin_double/3.jpg']),
                'content' => '<p>Cabin đôi lý tưởng cho cặp đôi hoặc gia đình nhỏ.</p>', 'priority' => 4,
            ],
            [
                'id' => 4, 'name' => 'Seater', 'model_name' => 'Seater Bus', 'seat_count' => 16,
                'services' => json_encode(['Điều hoà', 'Nước uống']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/seater/1.png',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/seater/1.png', '/userfiles/files/kingexpressbus/seater/2.png']),
                'content' => '<p>Xe ghế ngồi 16 chỗ phù hợp cho những chuyến đi ngắn, linh hoạt.</p>', 'priority' => 6,
            ],
            [
                'id' => 5, 'name' => 'Limousine', 'model_name' => 'Limousine Bus', 'seat_count' => 9,
                'services' => json_encode(['Nước uống', 'Wi-Fi', 'Điều hoà', 'Ổ sạc']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/limousine/1.png',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/limousine/1.png', '/userfiles/files/kingexpressbus/limousine/2.png']),
                'content' => '<p>Xe Limousine hiện đại với ghế hạng thương gia và dịch vụ cao cấp.</p>', 'priority' => 5,
            ],
            [
                'id' => 6, 'name' => 'VIP 32 sleeper', 'model_name' => 'VIP Sleeper 32', 'seat_count' => 32,
                'services' => json_encode(['Nước uống', 'Chăn gối', 'Rèm che']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-1.jpg',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-1.jpg', '/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-2.jpg']),
                'content' => '<p>Xe giường nằm VIP 32 chỗ với rèm che riêng tư, không gian sang trọng.</p>', 'priority' => 2,
            ],
             [
                'id' => 7, 'name' => 'VIP 22 cabin single', 'model_name' => 'VIP Cabin Single 22', 'seat_count' => 22,
                'services' => json_encode(['Nước uống', 'Rèm che', 'TV', 'Cổng sạc USB']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin/1.jpg',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/cabin/1.jpg', '/userfiles/files/kingexpressbus/cabin/2.jpg', '/userfiles/files/kingexpressbus/cabin/3.jpg']),
                'content' => '<p>Cabin VIP đơn 22 giường đem lại sự riêng tư tuyệt đối với đầy đủ tiện nghi.</p>', 'priority' => 3,
            ],
            [
                'id' => 8, 'name' => 'VIP 22 cabin double', 'model_name' => 'VIP Cabin Double 22', 'seat_count' => 22,
                'services' => json_encode(['Chăn gối', 'TV', 'Cổng sạc USB']),
                'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin_double/1.jpg',
                'image_list_url' => json_encode(['/userfiles/files/kingexpressbus/cabin_double/1.jpg', '/userfiles/files/kingexpressbus/cabin_double/2.jpg', '/userfiles/files/kingexpressbus/cabin_double/3.jpg']),
                'content' => '<p>Cabin VIP đôi 22 giường lý tưởng cho cặp đôi hoặc gia đình nhỏ.</p>', 'priority' => 4,
            ]
        ];

        $insertData = array_map(fn($item) => array_merge($item, ['company_id' => $companyId, 'seat_map' => null, 'created_at' => now(), 'updated_at' => now()]), $data);
        DB::table('buses')->insert($insertData);
        return $data;
    }

    private function seedRoutes(array $provinces): array
    {
        $provinceMap = collect($provinces)->pluck('id', 'name')->all();

        $routesList = [
            ['from' => 'Hà Nội', 'to' => 'Sapa', 'duration' => '6h', 'priority' => 1],
            ['from' => 'Sapa', 'to' => 'Hà Nội', 'duration' => '6h', 'priority' => 2],
            ['from' => 'Hà Nội', 'to' => 'Tuần Châu', 'duration' => '3,5h', 'priority' => 10],
            ['from' => 'Tuần Châu', 'to' => 'Hà Nội', 'duration' => '3,5h', 'priority' => 11],
            ['from' => 'Hà Nội', 'to' => 'Mai Châu', 'duration' => '3,5h', 'priority' => 12],
            ['from' => 'Mai Châu', 'to' => 'Hà Nội', 'duration' => '3,5h', 'priority' => 13],
            ['from' => 'Hà Nội', 'to' => 'Cát Bà', 'duration' => '3,5h', 'priority' => 8],
            ['from' => 'Cát Bà', 'to' => 'Hà Nội', 'duration' => '3,5h', 'priority' => 9],
            ['from' => 'Hà Nội', 'to' => 'Hà Giang', 'duration' => '6h', 'priority' => 3],
            ['from' => 'Hà Giang', 'to' => 'Hà Nội', 'duration' => '6h', 'priority' => 4],
            ['from' => 'Hà Nội', 'to' => 'Ninh Bình', 'duration' => '2h', 'priority' => 5],
            ['from' => 'Ninh Bình', 'to' => 'Hà Nội', 'duration' => '2h', 'priority' => 6],
            ['from' => 'Hà Nội', 'to' => 'Phong Nha', 'duration' => '9h', 'priority' => 14],
            ['from' => 'Phong Nha', 'to' => 'Hà Nội', 'duration' => '9h', 'priority' => 15],
            ['from' => 'Hà Nội', 'to' => 'Huế', 'duration' => '12h', 'priority' => 16],
            ['from' => 'Huế', 'to' => 'Hà Nội', 'duration' => '12h', 'priority' => 17],
            ['from' => 'Hà Nội', 'to' => 'Đà Nẵng', 'duration' => '15h', 'priority' => 18],
            ['from' => 'Đà Nẵng', 'to' => 'Hà Nội', 'duration' => '15h', 'priority' => 19],
            ['from' => 'Hà Nội', 'to' => 'Hội An', 'duration' => '16h', 'priority' => 20],
            ['from' => 'Hội An', 'to' => 'Hà Nội', 'duration' => '16h', 'priority' => 21],
            ['from' => 'Ninh Bình', 'to' => 'Phong Nha', 'duration' => '7h', 'priority' => 22],
            ['from' => 'Phong Nha', 'to' => 'Ninh Bình', 'duration' => '7h', 'priority' => 23],
            ['from' => 'Ninh Bình', 'to' => 'Huế', 'duration' => '10h', 'priority' => 24],
            ['from' => 'Huế', 'to' => 'Ninh Bình', 'duration' => '10h', 'priority' => 25],
            ['from' => 'Ninh Bình', 'to' => 'Đà Nẵng', 'duration' => '13h', 'priority' => 26],
            ['from' => 'Đà Nẵng', 'to' => 'Ninh Bình', 'duration' => '13h', 'priority' => 27],
            ['from' => 'Ninh Bình', 'to' => 'Hội An', 'duration' => '14h', 'priority' => 28],
            ['from' => 'Hội An', 'to' => 'Ninh Bình', 'duration' => '14h', 'priority' => 29],
            ['from' => 'Đà Nẵng', 'to' => 'Hội An', 'duration' => '1h', 'priority' => 30],
            ['from' => 'Hội An', 'to' => 'Đà Nẵng', 'duration' => '1h', 'priority' => 31],
            ['from' => 'Huế', 'to' => 'Hội An', 'duration' => '4h', 'priority' => 32],
            ['from' => 'Hội An', 'to' => 'Huế', 'duration' => '4h', 'priority' => 33],
            ['from' => 'Huế', 'to' => 'Đà Nẵng', 'duration' => '3h', 'priority' => 34],
            ['from' => 'Đà Nẵng', 'to' => 'Huế', 'duration' => '3h', 'priority' => 35],
            ['from' => 'Phong Nha', 'to' => 'Hội An', 'duration' => '7h', 'priority' => 36],
            ['from' => 'Hội An', 'to' => 'Phong Nha', 'duration' => '7h', 'priority' => 37],
            ['from' => 'Phong Nha', 'to' => 'Đà Nẵng', 'duration' => '6h', 'priority' => 38],
            ['from' => 'Đà Nẵng', 'to' => 'Phong Nha', 'duration' => '6h', 'priority' => 39],
            ['from' => 'Phong Nha', 'to' => 'Huế', 'duration' => '3,5h', 'priority' => 40],
            ['from' => 'Huế', 'to' => 'Phong Nha', 'duration' => '3,5h', 'priority' => 41],
            ['from' => 'Hà Giang', 'to' => 'Ninh Bình', 'duration' => '8h', 'priority' => 42],
            ['from' => 'Ninh Bình', 'to' => 'Hà Giang', 'duration' => '8h', 'priority' => 43],
            ['from' => 'Hà Giang', 'to' => 'Sapa', 'duration' => '7h', 'priority' => 7],
            ['from' => 'Sapa', 'to' => 'Hà Giang', 'duration' => '7h', 'priority' => 8],
            ['from' => 'Hà Giang', 'to' => 'Cát Bà', 'duration' => '12h', 'priority' => 44],
            ['from' => 'Cát Bà', 'to' => 'Hà Giang', 'duration' => '12h', 'priority' => 45],
        ];

        $insertData = [];
        $id = 1;
        foreach ($routesList as $route) {
            $startName = $route['from'];
            $endName = $route['to'];
            $routeName = $startName . ' - ' . $endName;
            $slug = Str::slug($routeName);

            $endProvince = collect($provinces)->firstWhere('name', $endName);
            $thumbnail = '/userfiles/files/city_imgs/' . ($endProvince['slug'] ?? Str::slug($endName)) . '.jpg';

            $insertData[] = [
                'id' => $id,
                'province_start_id' => $provinceMap[$startName],
                'province_end_id' => $provinceMap[$endName],
                'name' => $routeName,
                'slug' => $slug,
                'title' => 'Vé xe ' . $routeName,
                'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến ' . $routeName,
                'duration' => $route['duration'],
                'distance_km' => null,
                'thumbnail_url' => $thumbnail,
                'image_list_url' => json_encode([$thumbnail]),
                'content' => null,
                'priority' => $route['priority'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $id++;
        }

        DB::table('routes')->insert($insertData);
        return $insertData;
    }

    private function seedCompanyRoutes(array $routes, array $companies): array
    {
        $companyId = $companies[0]['id'];
        $companySlug = $companies[0]['slug'];

        $insertData = [];
        foreach ($routes as $route) {
            $insertData[] = [
                'id' => $route['id'],
                'company_id' => $companyId,
                'route_id' => $route['id'],
                'name' => $route['name'] . ' - ' . $companies[0]['name'],
                'slug' => $route['slug'] . '-' . $companySlug,
                'title' => $route['title'],
                'description' => $route['description'],
                'duration' => $route['duration'],
                'distance_km' => $route['distance_km'],
                'thumbnail_url' => $route['thumbnail_url'],
                'image_list_url' => $route['image_list_url'],
                'content' => $route['content'],
                'priority' => $route['priority'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('company_routes')->insert($insertData);
        return $insertData;
    }

    private function seedCompanyRouteStops(array $companyRoutes, array $routes, array $stops): void
    {
        $insertData = [];
        $stopMap = [
            1 => [1, 1], // Ha Noi -> VP Ha Noi
            2 => [2, 2], // Sapa -> VP Sapa
            3 => [1, 3], // Tuan Chau
            4 => [1, 4], // Mai Chau
            5 => [1, 5], // Cat Ba
            6 => [1, 6], // Ha Giang
            7 => [1, 7], // Ninh Binh
            8 => [1, 8], // Phong Nha
            9 => [1, 9], // Hue
            10 => [1, 10], // Da Nang
            11 => [1, 11] // Hoi An
        ];

        foreach ($companyRoutes as $cr) {
            $route = collect($routes)->firstWhere('id', $cr['route_id']);
            $startProvId = $route['province_start_id'];
            $endProvId = $route['province_end_id'];

            $pickupStopId = $stopMap[$startProvId][1] ?? null;
            $dropoffStopId = $stopMap[$endProvId][1] ?? null;

            if ($startProvId == 7) { // Ninh Binh has special pickup points for some routes
                 if (in_array($endProvId, [8, 9, 10, 11])) { // Phong Nha, Hue, Da Nang, Hoi An
                     $pickupStopId = 12; // VP Tam Coc
                 }
            }

            if ($pickupStopId) {
                $insertData[] = ['company_route_id' => $cr['id'], 'stop_id' => $pickupStopId, 'stop_type' => 'pickup', 'priority' => 1];
            }
            if ($dropoffStopId) {
                $insertData[] = ['company_route_id' => $cr['id'], 'stop_id' => $dropoffStopId, 'stop_type' => 'dropoff', 'priority' => 2];
            }
        }
        DB::table('company_route_stops')->insert($insertData);
    }

    private function getEndTime(string $startTime, string $durationStr): string
    {
        $time = Carbon::createFromTimeString($startTime);
        $durationStr = str_replace(',', '.', $durationStr);
        if (str_contains($durationStr, 'h')) {
            $hours = (float) str_replace('h', '', $durationStr);
            $minutes = ($hours - floor($hours)) * 60;
            $time->addHours(floor($hours))->addMinutes($minutes);
        }
        return $time->format('H:i:s');
    }

    private function seedBusRoutes(array $buses, array $companyRoutes, array $routes): array
    {
        $busTypeMap = collect($buses)->pluck('id', 'name')->all();
        $routeMap = collect($routes)->pluck('id', 'name')->all();

        $rawBusData = [
            ['from' => 'Hà Nội', 'to' => 'Sapa', 'time' => '07:00, 22:00', 'type' => 'Sleeper', 'price' => 270000],
            ['from' => 'Hà Nội', 'to' => 'Sapa', 'time' => '07:00, 22:00', 'type' => 'Cabin Single', 'price' => 400000],
            ['from' => 'Hà Nội', 'to' => 'Sapa', 'time' => '07:00, 22:00', 'type' => 'Cabin Double', 'price' => 650000],
            ['from' => 'Sapa', 'to' => 'Hà Nội', 'time' => '14:00, 16:00, 22:00', 'type' => 'Sleeper', 'price' => 270000],
            ['from' => 'Sapa', 'to' => 'Hà Nội', 'time' => '14:00, 16:00, 22:00', 'type' => 'Cabin Single', 'price' => 400000],
            ['from' => 'Sapa', 'to' => 'Hà Nội', 'time' => '14:00, 16:00, 22:00', 'type' => 'Cabin Double', 'price' => 650000],
            ['from' => 'Hà Nội', 'to' => 'Tuần Châu', 'time' => '07:00; 08:00', 'type' => 'Seater', 'price' => 200000],
            ['from' => 'Hà Nội', 'to' => 'Tuần Châu', 'time' => '07:00; 08:00', 'type' => 'Limousine', 'price' => 250000],
            ['from' => 'Tuần Châu', 'to' => 'Hà Nội', 'time' => '11:30', 'type' => 'Seater', 'price' => 200000],
            ['from' => 'Tuần Châu', 'to' => 'Hà Nội', 'time' => '11:30', 'type' => 'Limousine', 'price' => 250000],
            ['from' => 'Hà Nội', 'to' => 'Mai Châu', 'time' => '07:00; 12:00; 16:00', 'type' => 'Limousine', 'price' => 300000],
            ['from' => 'Mai Châu', 'to' => 'Hà Nội', 'time' => '04:00; 08:30; 13:00', 'type' => 'Limousine', 'price' => 300000],
            ['from' => 'Hà Nội', 'to' => 'Cát Bà', 'time' => '6:00, 7:00, 8:00, 9:00, 10:00, 11:00, 12:30, 13:30, 14:30, 15:30', 'type' => 'Seater', 'price' => 270000],
            ['from' => 'Hà Nội', 'to' => 'Cát Bà', 'time' => '6:30, 7:30, 8:30, 9:30, 11:30, 13:30, 14:30, 15:30, 16:00', 'type' => 'Limousine', 'price' => 320000],
            ['from' => 'Cát Bà', 'to' => 'Hà Nội', 'time' => '7:00, 8:00, 9:00, 10:30, 11:30, 12:30, 14:00, 15:00, 16:00, 17:00', 'type' => 'Seater', 'price' => 270000],
            ['from' => 'Cát Bà', 'to' => 'Hà Nội', 'time' => '5:00, 7:00, 9:00, 10:30, 12:00, 13:30, 14:30, 15:30, 17:00', 'type' => 'Limousine', 'price' => 320000],
            ['from' => 'Hà Nội', 'to' => 'Hà Giang', 'time' => '06:30; 15:30', 'type' => 'Limousine', 'price' => 370000],
            ['from' => 'Hà Nội', 'to' => 'Hà Giang', 'time' => '20:00', 'type' => 'Sleeper', 'price' => 320000],
            ['from' => 'Hà Nội', 'to' => 'Hà Giang', 'time' => '09:00; 19:30', 'type' => 'VIP 32 sleeper', 'price' => 370000],
            ['from' => 'Hà Nội', 'to' => 'Hà Giang', 'time' => '09:00; 11:30; 19:30', 'type' => 'VIP 22 cabin single', 'price' => 420000],
            ['from' => 'Hà Nội', 'to' => 'Hà Giang', 'time' => '09:00; 11:30; 19:30', 'type' => 'VIP 22 cabin double', 'price' => 600000],
            ['from' => 'Hà Giang', 'to' => 'Hà Nội', 'time' => '06:30; 15:30', 'type' => 'Limousine', 'price' => 370000],
            ['from' => 'Hà Giang', 'to' => 'Hà Nội', 'time' => '20:30', 'type' => 'Sleeper', 'price' => 320000],
            ['from' => 'Hà Giang', 'to' => 'Hà Nội', 'time' => '09:00; 18:00', 'type' => 'VIP 32 sleeper', 'price' => 370000],
            ['from' => 'Hà Giang', 'to' => 'Hà Nội', 'time' => '07:00; 09:00; 11:30; 15:30; 17:30; 19:30', 'type' => 'VIP 22 cabin single', 'price' => 420000],
            ['from' => 'Hà Giang', 'to' => 'Hà Nội', 'time' => '07:00; 09:00; 11:30; 15:30; 17:30; 19:30', 'type' => 'VIP 22 cabin double', 'price' => 600000],
            ['from' => 'Hà Nội', 'to' => 'Ninh Bình', 'time' => '6:10; 07:10; 08:10; 09:10; 10:10; 11:10; 12:10; 13:10; 14:10; 15:10; 16:10; 17:10', 'type' => 'Limousine', 'price' => 250000],
            ['from' => 'Ninh Bình', 'to' => 'Hà Nội', 'time' => '6:30; 07:30; 08:30; 09:30; 10:30; 11:30; 12:30; 13:30; 14:30; 15:30; 16:30; 17:30', 'type' => 'Limousine', 'price' => 250000],
            ['from' => 'Hà Nội', 'to' => 'Phong Nha', 'time' => '18:00', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Hà Nội', 'to' => 'Phong Nha', 'time' => '18:00', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Hà Nội', 'to' => 'Phong Nha', 'time' => '18:00', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Hà Nội', 'to' => 'Phong Nha', 'time' => '18:00', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Phong Nha', 'to' => 'Hà Nội', 'time' => '20:00', 'type' => 'VIP 32 sleeper', 'price' => 250000],
            ['from' => 'Phong Nha', 'to' => 'Hà Nội', 'time' => '21:30', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Phong Nha', 'to' => 'Hà Nội', 'time' => '21:30', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Phong Nha', 'to' => 'Hà Nội', 'time' => '21:30', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Phong Nha', 'to' => 'Hà Nội', 'time' => '21:30', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Hà Nội', 'to' => 'Huế', 'time' => '18:00', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Hà Nội', 'to' => 'Huế', 'time' => '18:00', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Hà Nội', 'to' => 'Huế', 'time' => '18:00', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Hà Nội', 'to' => 'Huế', 'time' => '18:00', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Huế', 'to' => 'Hà Nội', 'time' => '15:00', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Huế', 'to' => 'Hà Nội', 'time' => '17:00', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Huế', 'to' => 'Hà Nội', 'time' => '17:00', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Huế', 'to' => 'Hà Nội', 'time' => '17:00', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Huế', 'to' => 'Hà Nội', 'time' => '17:00', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Hà Nội', 'to' => 'Đà Nẵng', 'time' => '18:00', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Hà Nội', 'to' => 'Đà Nẵng', 'time' => '18:00', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Hà Nội', 'to' => 'Đà Nẵng', 'time' => '18:00', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Hà Nội', 'to' => 'Đà Nẵng', 'time' => '18:00', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Đà Nẵng', 'to' => 'Hà Nội', 'time' => '14:15', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Đà Nẵng', 'to' => 'Hà Nội', 'time' => '14:15', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Đà Nẵng', 'to' => 'Hà Nội', 'time' => '14:15', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Đà Nẵng', 'to' => 'Hà Nội', 'time' => '14:15', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Hà Nội', 'to' => 'Hội An', 'time' => '18:00', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Hà Nội', 'to' => 'Hội An', 'time' => '18:00', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Hà Nội', 'to' => 'Hội An', 'time' => '18:00', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Hà Nội', 'to' => 'Hội An', 'time' => '18:00', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Hội An', 'to' => 'Hà Nội', 'time' => '13:30', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Hội An', 'to' => 'Hà Nội', 'time' => '13:30', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Hội An', 'to' => 'Hà Nội', 'time' => '13:30', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Hội An', 'to' => 'Hà Nội', 'time' => '13:30', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Ninh Bình', 'to' => 'Phong Nha', 'time' => '20:30', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Ninh Bình', 'to' => 'Phong Nha', 'time' => '20:30', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Ninh Bình', 'to' => 'Phong Nha', 'time' => '20:30', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Ninh Bình', 'to' => 'Phong Nha', 'time' => '20:30', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Phong Nha', 'to' => 'Ninh Bình', 'time' => '20:00', 'type' => 'VIP 32 sleeper', 'price' => 250000],
            ['from' => 'Phong Nha', 'to' => 'Ninh Bình', 'time' => '21:30', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Phong Nha', 'to' => 'Ninh Bình', 'time' => '21:30', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Phong Nha', 'to' => 'Ninh Bình', 'time' => '21:30', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Phong Nha', 'to' => 'Ninh Bình', 'time' => '21:30', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Ninh Bình', 'to' => 'Huế', 'time' => '20:30', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Ninh Bình', 'to' => 'Huế', 'time' => '20:30', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Ninh Bình', 'to' => 'Huế', 'time' => '20:30', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Ninh Bình', 'to' => 'Huế', 'time' => '20:30', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Huế', 'to' => 'Ninh Bình', 'time' => '15:00', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Huế', 'to' => 'Ninh Bình', 'time' => '17:00', 'type' => 'Sleeper', 'price' => 380000],
            ['from' => 'Huế', 'to' => 'Ninh Bình', 'time' => '17:00', 'type' => 'VIP 32 sleeper', 'price' => 450000],
            ['from' => 'Huế', 'to' => 'Ninh Bình', 'time' => '17:00', 'type' => 'VIP 22 cabin single', 'price' => 550000],
            ['from' => 'Huế', 'to' => 'Ninh Bình', 'time' => '17:00', 'type' => 'VIP 22 cabin double', 'price' => 750000],
            ['from' => 'Ninh Bình', 'to' => 'Đà Nẵng', 'time' => '20:30', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Ninh Bình', 'to' => 'Đà Nẵng', 'time' => '20:30', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Ninh Bình', 'to' => 'Đà Nẵng', 'time' => '20:30', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Ninh Bình', 'to' => 'Đà Nẵng', 'time' => '20:30', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Đà Nẵng', 'to' => 'Ninh Bình', 'time' => '14:15', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Đà Nẵng', 'to' => 'Ninh Bình', 'time' => '14:15', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Đà Nẵng', 'to' => 'Ninh Bình', 'time' => '14:15', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Đà Nẵng', 'to' => 'Ninh Bình', 'time' => '14:15', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Ninh Bình', 'to' => 'Hội An', 'time' => '20:30', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Ninh Bình', 'to' => 'Hội An', 'time' => '20:30', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Ninh Bình', 'to' => 'Hội An', 'time' => '20:30', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Ninh Bình', 'to' => 'Hội An', 'time' => '20:30', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Hội An', 'to' => 'Ninh Bình', 'time' => '13:30', 'type' => 'Sleeper', 'price' => 450000],
            ['from' => 'Hội An', 'to' => 'Ninh Bình', 'time' => '13:30', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Hội An', 'to' => 'Ninh Bình', 'time' => '13:30', 'type' => 'VIP 22 cabin single', 'price' => 750000],
            ['from' => 'Hội An', 'to' => 'Ninh Bình', 'time' => '13:30', 'type' => 'VIP 22 cabin double', 'price' => 1050000],
            ['from' => 'Đà Nẵng', 'to' => 'Hội An', 'time' => '11:00', 'type' => 'Sleeper', 'price' => 150000],
            ['from' => 'Đà Nẵng', 'to' => 'Hội An', 'time' => '11:00', 'type' => 'VIP 32 sleeper', 'price' => 200000],
            ['from' => 'Hội An', 'to' => 'Đà Nẵng', 'time' => '13:30', 'type' => 'Sleeper', 'price' => 150000],
            ['from' => 'Hội An', 'to' => 'Đà Nẵng', 'time' => '13:30', 'type' => 'VIP 32 sleeper', 'price' => 200000],
            ['from' => 'Huế', 'to' => 'Hội An', 'time' => '07:30', 'type' => 'Sleeper', 'price' => 200000],
            ['from' => 'Huế', 'to' => 'Hội An', 'time' => '07:30', 'type' => 'VIP 32 sleeper', 'price' => 250000],
            ['from' => 'Hội An', 'to' => 'Huế', 'time' => '13:30', 'type' => 'Sleeper', 'price' => 200000],
            ['from' => 'Hội An', 'to' => 'Huế', 'time' => '13:30', 'type' => 'VIP 32 sleeper', 'price' => 250000],
            ['from' => 'Huế', 'to' => 'Đà Nẵng', 'time' => '07:30', 'type' => 'Sleeper', 'price' => 200000],
            ['from' => 'Huế', 'to' => 'Đà Nẵng', 'time' => '07:30', 'type' => 'VIP 32 sleeper', 'price' => 250000],
            ['from' => 'Đà Nẵng', 'to' => 'Huế', 'time' => '14:15', 'type' => 'Sleeper', 'price' => 200000],
            ['from' => 'Đà Nẵng', 'to' => 'Huế', 'time' => '14:15', 'type' => 'VIP 32 sleeper', 'price' => 250000],
            ['from' => 'Phong Nha', 'to' => 'Hội An', 'time' => '04:00', 'type' => 'Sleeper', 'price' => 300000],
            ['from' => 'Phong Nha', 'to' => 'Hội An', 'time' => '04:00', 'type' => 'VIP 32 sleeper', 'price' => 400000],
            ['from' => 'Hội An', 'to' => 'Phong Nha', 'time' => '13:30', 'type' => 'Sleeper', 'price' => 300000],
            ['from' => 'Hội An', 'to' => 'Phong Nha', 'time' => '13:30', 'type' => 'VIP 32 sleeper', 'price' => 400000],
            ['from' => 'Phong Nha', 'to' => 'Đà Nẵng', 'time' => '04:00', 'type' => 'Sleeper', 'price' => 300000],
            ['from' => 'Phong Nha', 'to' => 'Đà Nẵng', 'time' => '04:00', 'type' => 'VIP 32 sleeper', 'price' => 400000],
            ['from' => 'Đà Nẵng', 'to' => 'Phong Nha', 'time' => '14:15', 'type' => 'Sleeper', 'price' => 300000],
            ['from' => 'Đà Nẵng', 'to' => 'Phong Nha', 'time' => '14:15', 'type' => 'VIP 32 sleeper', 'price' => 400000],
            ['from' => 'Phong Nha', 'to' => 'Huế', 'time' => '04:00', 'type' => 'Sleeper', 'price' => 250000],
            ['from' => 'Phong Nha', 'to' => 'Huế', 'time' => '04:00', 'type' => 'VIP 32 sleeper', 'price' => 300000],
            ['from' => 'Huế', 'to' => 'Phong Nha', 'time' => '17:00', 'type' => 'Sleeper', 'price' => 250000],
            ['from' => 'Huế', 'to' => 'Phong Nha', 'time' => '17:00', 'type' => 'VIP 32 sleeper', 'price' => 300000],
            ['from' => 'Hà Giang', 'to' => 'Ninh Bình', 'time' => '20:00', 'type' => 'Sleeper', 'price' => 370000],
            ['from' => 'Hà Giang', 'to' => 'Ninh Bình', 'time' => '20:00', 'type' => 'VIP 22 cabin single', 'price' => 460000],
            ['from' => 'Hà Giang', 'to' => 'Ninh Bình', 'time' => '20:00', 'type' => 'VIP 22 cabin double', 'price' => 640000],
            ['from' => 'Ninh Bình', 'to' => 'Hà Giang', 'time' => '18:00', 'type' => 'Sleeper', 'price' => 370000],
            ['from' => 'Ninh Bình', 'to' => 'Hà Giang', 'time' => '19:00', 'type' => 'VIP 22 cabin single', 'price' => 460000],
            ['from' => 'Ninh Bình', 'to' => 'Hà Giang', 'time' => '19:00', 'type' => 'VIP 22 cabin double', 'price' => 640000],
            ['from' => 'Hà Giang', 'to' => 'Sapa', 'time' => '09:00', 'type' => 'Limousine', 'price' => 370000],
            ['from' => 'Hà Giang', 'to' => 'Sapa', 'time' => '18:00', 'type' => 'VIP 32 sleeper', 'price' => 370000],
            ['from' => 'Hà Giang', 'to' => 'Sapa', 'time' => '18:00', 'type' => 'VIP 22 cabin single', 'price' => 420000],
            ['from' => 'Hà Giang', 'to' => 'Sapa', 'time' => '18:00', 'type' => 'VIP 22 cabin double', 'price' => 600000],
            ['from' => 'Sapa', 'to' => 'Hà Giang', 'time' => '11:00', 'type' => 'VIP 32 sleeper', 'price' => 370000],
            ['from' => 'Sapa', 'to' => 'Hà Giang', 'time' => '11:00', 'type' => 'VIP 22 cabin single', 'price' => 420000],
            ['from' => 'Sapa', 'to' => 'Hà Giang', 'time' => '11:00', 'type' => 'VIP 22 cabin double', 'price' => 600000],
            ['from' => 'Sapa', 'to' => 'Hà Giang', 'time' => '17:00', 'type' => 'Limousine', 'price' => 370000],
            ['from' => 'Hà Giang', 'to' => 'Cát Bà', 'time' => '19:00', 'type' => 'VIP 32 sleeper', 'price' => 550000],
            ['from' => 'Hà Giang', 'to' => 'Cát Bà', 'time' => '19:00', 'type' => 'VIP 22 cabin single', 'price' => 600000],
            ['from' => 'Hà Giang', 'to' => 'Cát Bà', 'time' => '19:00', 'type' => 'VIP 22 cabin double', 'price' => 800000],
            ['from' => 'Cát Bà', 'to' => 'Hà Giang', 'time' => '17:00', 'type' => 'VIP 32 sleeper', 'price' => 550000],
        ];

        $insertData = [];
        $id = 1;
        foreach ($rawBusData as $data) {
            $routeName = $data['from'] . ' - ' . $data['to'];
            $routeInfo = collect($routes)->firstWhere('name', $routeName);

            if (!$routeInfo || !isset($busTypeMap[$data['type']])) {
                continue;
            }

            $companyRouteId = $routeMap[$routeName];
            $busId = $busTypeMap[$data['type']];
            $price = $data['price'];
            $duration = $routeInfo['duration'];

            $times = preg_split('/[,;]\s*/', $data['time']);

            foreach ($times as $startTime) {
                $startTime = trim($startTime);
                if (empty($startTime)) continue;

                $endTime = $this->getEndTime($startTime, $duration);

                $insertData[] = [
                    'id' => $id++,
                    'bus_id' => $busId,
                    'company_route_id' => $companyRouteId,
                    'start_time' => $startTime . ':00',
                    'end_time' => $endTime,
                    'price' => $price,
                    'is_active' => true,
                    'priority' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('bus_routes')->insert($insertData);
        return $insertData;
    }

    private function seedMenus(array $routes): void
    {
        $menuItems = [];
        $menuItems[] = ['id' => 1, 'name' => 'Trang chủ', 'url' => '/', 'parent_id' => null, 'priority' => 0, 'type' => 'custom_link', 'related_id' => null];
        $menuItems[] = ['id' => 2, 'name' => 'Tuyến đường', 'url' => '#', 'parent_id' => null, 'priority' => 1, 'type' => 'custom_link', 'related_id' => null];
        $menuItems[] = ['id' => 3, 'name' => 'Liên Hệ', 'url' => '/lien-he', 'parent_id' => null, 'priority' => 2, 'type' => 'custom_link', 'related_id' => null];

        $nextId = 4;
        $sortedRoutes = collect($routes)->sortBy('priority')->values();

        foreach ($sortedRoutes as $index => $route) {
            $menuItems[] = [
                'id' => $nextId++,
                'name' => $route['name'],
                'url' => '/tuyen-duong/' . $route['slug'],
                'parent_id' => 2,
                'priority' => $index,
                'type' => 'route',
                'related_id' => $route['id'],
            ];
        }

        $insertData = array_map(function($item) {
            $item['created_at'] = now();
            $item['updated_at'] = now();
            return $item;
        }, $menuItems);

        DB::table('menus')->insert($insertData);
    }

    private function seedBookings(array $busRoutes): void
    {
        if (empty($busRoutes)) {
            return;
        }

        $stops = DB::table('stops')->get();
        $busRoutes = collect($busRoutes)->keyBy('id');
        $companyRoutes = DB::table('company_routes')->get()->keyBy('id');
        $routes = DB::table('routes')->get()->keyBy('id');

        $bookings = [
            ['id' => 1, 'user_id' => 2, 'bus_route_id' => 1, 'booking_date' => '2025-10-20', 'quantity' => 2, 'status' => 'confirmed', 'payment_method' => 'online_banking', 'payment_status' => 'paid'],
            ['id' => 2, 'user_id' => 3, 'bus_route_id' => 10, 'booking_date' => '2025-11-15', 'quantity' => 1, 'status' => 'pending', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid'],
            ['id' => 3, 'user_id' => 5, 'bus_route_id' => 3, 'booking_date' => '2025-09-25', 'quantity' => 1, 'status' => 'confirmed', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid'],
        ];

        $users = DB::table('users')->get()->keyBy('id');
        $insertData = [];

        foreach ($bookings as $booking) {
            $user = $users->get($booking['user_id']);
            $busRoute = $busRoutes->get($booking['bus_route_id']);
            if (!$user || !$busRoute) continue;

            $companyRoute = $companyRoutes->get($busRoute['company_route_id']);
            $route = $routes->get($companyRoute->route_id);

            $crStops = DB::table('company_route_stops')->where('company_route_id', $companyRoute->id)->get();
            $pickupStop = $crStops->firstWhere('stop_type', 'pickup');
            $dropoffStop = $crStops->firstWhere('stop_type', 'dropoff');

            if (!$pickupStop || !$dropoffStop) continue;

            $insertData[] = [
                'id' => $booking['id'],
                'booking_code' => 'KEB' . Str::upper(Str::random(8)),
                'user_id' => $booking['user_id'],
                'bus_route_id' => $booking['bus_route_id'],
                'booking_date' => $booking['booking_date'],
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone ?? '0987654321',
                'pickup_stop_id' => $pickupStop->stop_id,
                'dropoff_stop_id' => $dropoffStop->stop_id,
                'quantity' => $booking['quantity'],
                'total_price' => $booking['quantity'] * $busRoute['price'],
                'status' => $booking['status'],
                'payment_method' => $booking['payment_method'],
                'payment_status' => $booking['payment_status'],
                'notes' => 'Ghi chú cho đơn hàng ' . $booking['id'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('bookings')->insert($insertData);
    }
}
