<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại để truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $this->truncateTables();

        // Chạy các seeder con theo đúng thứ tự phụ thuộc
        $this->seedUsers();
        $this->seedWebProfiles();
        $this->seedDistrictTypes();
        $this->seedBusServices();
        $this->seedProvinces();
        $this->seedDistricts();
        $this->seedStops();
        $this->seedCompanies();
        $this->seedBuses();
        $this->seedRoutes();
        $this->seedCompanyRoutes();
        $this->seedCompanyRouteStops();
        $this->seedBusRoutes();
        $this->seedMenus();
        $this->seedBookings();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Xóa dữ liệu cũ trong các bảng.
     */
    private function truncateTables(): void
    {
        $tables = [
            'bookings',
            'bus_routes',
            'company_route_stops',
            'company_routes',
            'buses',
            'companies',
            'routes',
            'stops',
            'districts',
            'bus_services',
            'district_types',
            'provinces',
            'menus',
            'web_profiles',
            'users',
            'cache',
            'cache_locks',
            'failed_jobs',
            'jobs',
            'job_batches',
            'migrations',
            'password_reset_tokens',
            'personal_access_tokens',
            'sessions',
        ];

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
    }

    private function seedUsers(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Admin KingExpress',
                'email' => 'admin@kingexpressbus.com',
                'phone' => '0865095066',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'email_verified_at' => now(),
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'remember_token' => Str::random(60),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@gmail.com',
                'phone' => '0123456789',
                'address' => '123 Đường Cầu Giấy, Hà Nội',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@gmail.com',
                'phone' => '0987123456',
                'address' => '456 Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'King Express Bus',
                'email' => 'company@kingexpressbus.com',
                'phone' => '0924300366',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'email_verified_at' => now(),
                'password' => Hash::make('admin'),
                'role' => 'company',
                'remember_token' => Str::random(60),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    private function seedWebProfiles(): void
    {
        DB::table('web_profiles')->insert([
            [
                'id' => 1,
                'profile_name' => 'Cấu hình mặc định',
                'is_default' => 1,
                'title' => 'King Express Bus - Nhà xe chất lượng cao',
                'description' => 'Chuyên cung cấp dịch vụ vận tải hành khách tuyến Bắc - Nam với dòng xe limousine và giường nằm cao cấp. An toàn, tiện nghi và đúng giờ.',
                'logo_url' => '/userfiles/files/web%20information/logo.jpg',
                'favicon_url' => null,
                'email' => 'kingexpressbus@gmail.com',
                'phone' => '0924300366',
                'hotline' => '0924300366',
                'whatsapp' => '0865095066',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'facebook_url' => 'https://www.facebook.com/HanoiSapa',
                'zalo_url' => 'https://zalo.me/0924300366',
                'map_embedded' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2056.7461655506627!2d105.84669827498637!3d21.03349741327072!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135abbe60e203cb%3A0xad322a16a1be4362!2zMTkgUC4gSMOgbmcgVGhp4bq_YywgSMOgbmcgR2FpLCBIb8OgbiBLaeG6v20sIEjDoCBO4buZaSAxMTA3MDEsIFZpZXRuYW0!5e0!3m2!1sen!2s!4v1759471064920!5m2!1sen!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                'policy_content' => '<h1>Chính sách chung</h1><p>Chính sách và điều khoản của nhà xe King Express Bus</p>',
                'introduction_content' => '<h2>Về chúng tôi</h2><p>King Express Bus tự hào là một trong những đơn vị vận tải hàng đầu</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    private function seedBusServices(): void
    {
        DB::table('bus_services')->insert([
            ['name' => 'Chăn gối', 'icon' => 'fas fa-bed', 'priority' => 1],
            ['name' => 'Nước uống', 'icon' => 'fas fa-tint', 'priority' => 2],
            ['name' => 'Điều hòa', 'icon' => 'fas fa-wind', 'priority' => 3],
            ['name' => 'Rèm che', 'icon' => 'fas fa-blinds-raised', 'priority' => 4],
            ['name' => 'TV', 'icon' => 'fas fa-tv', 'priority' => 5],
            ['name' => 'Cổng sạc USB', 'icon' => 'fas fa-usb', 'priority' => 6],
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

    private function seedProvinces(): void
    {
        DB::table('provinces')->insert([
            ['id' => 1, 'name' => 'Hà Nội', 'slug' => 'ha-noi', 'priority' => 100, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Sapa', 'slug' => 'sapa', 'priority' => 99, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Tuần Châu', 'slug' => 'tuan-chau', 'priority' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Mai Châu', 'slug' => 'mai-chau', 'priority' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Cát Bà', 'slug' => 'cat-ba', 'priority' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Hà Giang', 'slug' => 'ha-giang', 'priority' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Ninh Bình', 'slug' => 'ninh-binh', 'priority' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Phong Nha', 'slug' => 'phong-nha', 'priority' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'name' => 'Huế', 'slug' => 'hue', 'priority' => 98, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'name' => 'Đà Nẵng', 'slug' => 'da-nang', 'priority' => 97, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'name' => 'Hội An', 'slug' => 'hoi-an', 'priority' => 11, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedDistricts(): void
    {
        DB::table('districts')->insert([
            ['id' => 1, 'province_id' => 1, 'district_type_id' => 6, 'name' => 'Thành Phố Hà Nội', 'slug' => 'thanh-pho-ha-noi', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'province_id' => 2, 'district_type_id' => 6, 'name' => 'Thị xã Sapa', 'slug' => 'thi-xa-sapa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'province_id' => 3, 'district_type_id' => 6, 'name' => 'Thành Phố Tuần Châu', 'slug' => 'thanh-pho-tuan-chau', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'province_id' => 4, 'district_type_id' => 6, 'name' => 'Huyện Mai Châu', 'slug' => 'huyen-mai-chau', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'province_id' => 5, 'district_type_id' => 6, 'name' => 'Huyện Cát Bà', 'slug' => 'huyen-cat-ba', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'province_id' => 6, 'district_type_id' => 6, 'name' => 'Thành Phố Hà Giang', 'slug' => 'thanh-pho-ha-giang', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'province_id' => 7, 'district_type_id' => 6, 'name' => 'Thành Phố Ninh Bình', 'slug' => 'thanh-pho-ninh-binh', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'province_id' => 8, 'district_type_id' => 6, 'name' => 'Huyện Phong Nha', 'slug' => 'huyen-phong-nha', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'province_id' => 9, 'district_type_id' => 6, 'name' => 'Thành Phố Huế', 'slug' => 'thanh-pho-hue', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'province_id' => 10, 'district_type_id' => 6, 'name' => 'Thành Phố Đà Nẵng', 'slug' => 'thanh-pho-da-nang', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'province_id' => 11, 'district_type_id' => 6, 'name' => 'Thành Phố Hội An', 'slug' => 'thanh-pho-hoi-an', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedStops(): void
    {
        DB::table('stops')->insert([
            ['id' => 1, 'district_id' => 1, 'name' => 'VP Hà Nội', 'address' => '19 Hàng Thiếc, Hoàn Kiếm, Hà Nội', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'district_id' => 2, 'name' => 'VP Sapa', 'address' => '458 Điện Biên Phủ, Sapa', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'district_id' => 3, 'name' => 'VP Tuần Châu', 'address' => 'Tuần Châu Harbor, Tuần Châu', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'district_id' => 4, 'name' => 'VP Mai Châu', 'address' => 'Hotel, Mai Châu', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'district_id' => 5, 'name' => 'VP Cát Bà', 'address' => '217 Một Tháng Tư, Cát Bà', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'district_id' => 6, 'name' => 'VP Hà Giang', 'address' => '100 Trần Phú, TP Hà Giang', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'district_id' => 7, 'name' => 'VP Ninh Bình', 'address' => 'Số 2A, đường 27/7, TP Ninh Bình', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'district_id' => 8, 'name' => 'VP Phong Nha', 'address' => 'Central Backpacker, Phong Nha', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 9, 'district_id' => 9, 'name' => 'VP Huế', 'address' => '07 Đội Cung, TP Huế', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 10, 'district_id' => 10, 'name' => 'VP Đà Nẵng', 'address' => 'Số 28 đường 3/2, TP Đà Nẵng', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 11, 'district_id' => 11, 'name' => 'VP Hội An', 'address' => '105 Tôn Đức Thắng, TP Hội An', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 12, 'district_id' => 7, 'name' => 'VP Tam Cốc', 'address' => 'Travel Agency, Tam Cốc, Ninh Bình', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedCompanies(): void
    {
        DB::table('companies')->insert([
            [
                'id' => 1,
                'user_id' => 6,
                'name' => 'King Express Bus',
                'slug' => 'king-express-bus',
                'title' => 'Nhà xe King Express Bus',
                'description' => 'Dịch vụ xe khách chất lượng cao',
                'thumbnail_url' => '/userfiles/files/web%20information/logo.jpg',
                'content' => '<p>Giới thiệu về nhà xe King Express Bus.</p>',
                'phone' => '0924300366',
                'hotline' => '0924300366',
                'email' => 'kingexpressbus@gmail.com',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'priority' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    private function seedBuses(): void
    {
        DB::table('buses')->insert([
            ['id' => 1, 'company_id' => 1, 'name' => 'Sleeper', 'model_name' => 'Sleeper Bus', 'seat_count' => 38, 'services' => '["Chăn gối", "Nước uống", "Điều hòa"]', 'priority' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'company_id' => 1, 'name' => 'VIP 32 sleeper', 'model_name' => 'VIP Sleeper 32', 'seat_count' => 32, 'services' => '["Nước uống", "Chăn gối", "Rèm che", "Điều hòa"]', 'priority' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'company_id' => 1, 'name' => 'VIP 22 Cabin Single', 'model_name' => 'VIP Cabin Single 22', 'seat_count' => 22, 'services' => '["Chăn gối", "Nước uống", "Rèm che", "TV", "Cổng sạc USB", "Điều hòa"]', 'priority' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'company_id' => 1, 'name' => 'VIP 22 Cabin Double', 'model_name' => 'VIP Cabin Double 22', 'seat_count' => 22, 'services' => '["Chăn gối", "Nước uống", "Cổng sạc USB", "Rèm che", "Điều hòa"]', 'priority' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'company_id' => 1, 'name' => 'Seater', 'model_name' => 'Seater Bus', 'seat_count' => 16, 'services' => '["Điều hoà", "Nước uống"]', 'priority' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'company_id' => 1, 'name' => 'Limousine', 'model_name' => 'Limousine Bus', 'seat_count' => 9, 'services' => '["Nước uống", "Điều hoà"]', 'priority' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedRoutes(): void
    {
        $routes = [
            // id => [start_id, end_id, name, slug, duration]
            1 => [1, 2, 'Hà Nội - Sapa', 'ha-noi-sapa', '6h'],
            2 => [2, 1, 'Sapa - Hà Nội', 'sapa-ha-noi', '6h'],
            3 => [1, 9, 'Hà Nội - Huế', 'ha-noi-hue', '12h'],
            4 => [1, 10, 'Hà Nội - Đà Nẵng', 'ha-noi-da-nang', '15h'],
            5 => [1, 6, 'Hà Nội - Hà Giang', 'ha-noi-ha-giang', '6h'],
            6 => [6, 1, 'Hà Giang - Hà Nội', 'ha-giang-ha-noi', '6h'],
            7 => [1, 7, 'Hà Nội - Ninh Bình', 'ha-noi-ninh-binh', '2h'],
            8 => [7, 1, 'Ninh Bình - Hà Nội', 'ninh-binh-ha-noi', '2h'],
            9 => [6, 2, 'Hà Giang - Sapa', 'ha-giang-sapa', '7h'],
            10 => [2, 6, 'Sapa - Hà Giang', 'sapa-ha-giang', '7h'],
            11 => [1, 5, 'Hà Nội - Cát Bà', 'ha-noi-cat-ba', '3.5h'],
            12 => [5, 1, 'Cát Bà - Hà Nội', 'cat-ba-ha-noi', '3.5h'],
            13 => [1, 3, 'Hà Nội - Tuần Châu', 'ha-noi-tuan-chau', '3.5h'],
            14 => [3, 1, 'Tuần Châu - Hà Nội', 'tuan-chau-ha-noi', '3.5h'],
            15 => [1, 4, 'Hà Nội - Mai Châu', 'ha-noi-mai-chau', '3.5h'],
            16 => [4, 1, 'Mai Châu - Hà Nội', 'mai-chau-ha-noi', '3.5h'],
            17 => [9, 1, 'Huế - Hà Nội', 'hue-ha-noi', '12h'],
            18 => [10, 1, 'Đà Nẵng - Hà Nội', 'da-nang-ha-noi', '15h'],
            19 => [1, 8, 'Hà Nội - Phong Nha', 'ha-noi-phong-nha', '9h'],
            20 => [8, 1, 'Phong Nha - Hà Nội', 'phong-nha-ha-noi', '9h'],
            21 => [1, 11, 'Hà Nội - Hội An', 'ha-noi-hoi-an', '16h'],
            22 => [11, 1, 'Hội An - Hà Nội', 'hoi-an-ha-noi', '16h'],
            23 => [7, 8, 'Ninh Bình - Phong Nha', 'ninh-binh-phong-nha', '7h'],
            24 => [8, 7, 'Phong Nha - Ninh Bình', 'phong-nha-ninh-binh', '7h'],
            25 => [7, 9, 'Ninh Bình - Huế', 'ninh-binh-hue', '10h'],
            26 => [9, 7, 'Huế - Ninh Bình', 'hue-ninh-binh', '10h'],
            27 => [7, 10, 'Ninh Bình - Đà Nẵng', 'ninh-binh-da-nang', '13h'],
            28 => [10, 7, 'Đà Nẵng - Ninh Bình', 'da-nang-ninh-binh', '13h'],
            29 => [7, 11, 'Ninh Bình - Hội An', 'ninh-binh-hoi-an', '14h'],
            30 => [11, 7, 'Hội An - Ninh Bình', 'hoi-an-ninh-binh', '14h'],
            31 => [10, 11, 'Đà Nẵng - Hội An', 'da-nang-hoi-an', '1h'],
            32 => [11, 10, 'Hội An - Đà Nẵng', 'hoi-an-da-nang', '1h'],
            33 => [9, 11, 'Huế - Hội An', 'hue-hoi-an', '4h'],
            34 => [11, 9, 'Hội An - Huế', 'hoi-an-hue', '4h'],
            35 => [9, 10, 'Huế - Đà Nẵng', 'hue-da-nang', '3h'],
            36 => [10, 9, 'Đà Nẵng - Huế', 'da-nang-hue', '3h'],
            37 => [8, 11, 'Phong Nha - Hội An', 'phong-nha-hoi-an', '7h'],
            38 => [11, 8, 'Hội An - Phong Nha', 'hoi-an-phong-nha', '7h'],
            39 => [8, 10, 'Phong Nha - Đà Nẵng', 'phong-nha-da-nang', '6h'],
            40 => [10, 8, 'Đà Nẵng - Phong Nha', 'da-nang-phong-nha', '6h'],
            41 => [8, 9, 'Phong Nha - Huế', 'phong-nha-hue', '3.5h'],
            42 => [9, 8, 'Huế - Phong Nha', 'hue-phong-nha', '3.5h'],
            43 => [6, 7, 'Hà Giang - Ninh Bình', 'ha-giang-ninh-binh', '8h'],
            44 => [7, 6, 'Ninh Bình - Hà Giang', 'ninh-binh-ha-giang', '8h'],
            45 => [6, 5, 'Hà Giang - Cát Bà', 'ha-giang-cat-ba', '12h'],
            46 => [5, 6, 'Cát Bà - Hà Giang', 'cat-ba-ha-giang', '12h'],
        ];

        $data = [];
        foreach ($routes as $id => $route) {
            $data[] = [
                'id' => $id,
                'province_start_id' => $route[0],
                'province_end_id' => $route[1],
                'name' => $route[2],
                'slug' => $route[3],
                'duration' => $route[4],
                'title' => 'Vé xe ' . $route[2],
                'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến ' . $route[2],
                'priority' => $id,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('routes')->insert($data);
    }

    private function seedCompanyRoutes(): void
    {
        $data = [];
        for ($i = 1; $i <= 46; $i++) {
            $route = DB::table('routes')->find($i);
            $data[] = [
                'id' => $i,
                'company_id' => 1,
                'route_id' => $i,
                'name' => $route->name . ' - King Express Bus',
                'slug' => $route->slug . '-king-express-bus',
                'title' => $route->title,
                'description' => $route->description,
                'duration' => $route->duration,
                'available_hotel_pickup' => ($i == 1), // Only enable for Ha Noi - Sapa as an example
                'priority' => $i,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        DB::table('company_routes')->insert($data);
    }

    private function seedCompanyRouteStops(): void
    {
        $stops = [
            // company_route_id => [pickup_stop_id, dropoff_stop_id]
            1 => [1, 2], 2 => [2, 1], 3 => [1, 9], 4 => [1, 10], 5 => [1, 6], 6 => [6, 1],
            7 => [1, 7], 8 => [7, 1], 9 => [6, 2], 10 => [2, 6], 11 => [1, 5], 12 => [5, 1],
            13 => [1, 3], 14 => [3, 1], 15 => [1, 4], 16 => [4, 1], 17 => [9, 1], 18 => [10, 1],
            19 => [1, 8], 20 => [8, 1], 21 => [1, 11], 22 => [11, 1], 23 => [12, 8], 24 => [8, 7],
            25 => [12, 9], 26 => [9, 7], 27 => [12, 10], 28 => [10, 7], 29 => [12, 11], 30 => [11, 7],
            31 => [10, 11], 32 => [11, 10], 33 => [9, 11], 34 => [11, 9], 35 => [9, 10], 36 => [10, 9],
            37 => [8, 11], 38 => [11, 8], 39 => [8, 10], 40 => [10, 8], 41 => [8, 9], 42 => [9, 8],
            43 => [6, 7], 44 => [7, 6], 45 => [6, 5], 46 => [5, 6],
        ];

        $data = [];
        $idCounter = 1;
        foreach ($stops as $routeId => $stopIds) {
            $data[] = ['id' => $idCounter++, 'company_route_id' => $routeId, 'stop_id' => $stopIds[0], 'stop_type' => 'pickup', 'priority' => 1];
            $data[] = ['id' => $idCounter++, 'company_route_id' => $routeId, 'stop_id' => $stopIds[1], 'stop_type' => 'dropoff', 'priority' => 2];
        }
        DB::table('company_route_stops')->insert($data);
    }

    private function seedBusRoutes(): void
    {
        $data = [
            // Tuyến 1: Hà Nội - Sapa
            ['bus_id' => 1, 'company_route_id' => 1, 'start_time' => '07:00', 'end_time' => '13:00', 'price' => 270000],
            ['bus_id' => 1, 'company_route_id' => 1, 'start_time' => '22:00', 'end_time' => '04:00', 'price' => 270000],
            ['bus_id' => 3, 'company_route_id' => 1, 'start_time' => '07:00', 'end_time' => '13:00', 'price' => 400000],
            ['bus_id' => 3, 'company_route_id' => 1, 'start_time' => '22:00', 'end_time' => '04:00', 'price' => 400000],
            ['bus_id' => 4, 'company_route_id' => 1, 'start_time' => '07:00', 'end_time' => '13:00', 'price' => 650000],
            ['bus_id' => 4, 'company_route_id' => 1, 'start_time' => '22:00', 'end_time' => '04:00', 'price' => 650000],

            // Tuyến 2: Sapa - Hà Nội
            ['bus_id' => 1, 'company_route_id' => 2, 'start_time' => '14:00', 'end_time' => '20:00', 'price' => 270000],
            ['bus_id' => 1, 'company_route_id' => 2, 'start_time' => '16:00', 'end_time' => '22:00', 'price' => 270000],
            ['bus_id' => 1, 'company_route_id' => 2, 'start_time' => '22:00', 'end_time' => '04:00', 'price' => 270000],
            ['bus_id' => 3, 'company_route_id' => 2, 'start_time' => '14:00', 'end_time' => '20:00', 'price' => 400000],
            ['bus_id' => 3, 'company_route_id' => 2, 'start_time' => '16:00', 'end_time' => '22:00', 'price' => 400000],
            ['bus_id' => 3, 'company_route_id' => 2, 'start_time' => '22:00', 'end_time' => '04:00', 'price' => 400000],
            ['bus_id' => 4, 'company_route_id' => 2, 'start_time' => '14:00', 'end_time' => '20:00', 'price' => 650000],
            ['bus_id' => 4, 'company_route_id' => 2, 'start_time' => '16:00', 'end_time' => '22:00', 'price' => 650000],
            ['bus_id' => 4, 'company_route_id' => 2, 'start_time' => '22:00', 'end_time' => '04:00', 'price' => 650000],

            // Tuyến 3: Hà Nội - Huế
            ['bus_id' => 1, 'company_route_id' => 3, 'start_time' => '18:00', 'end_time' => '06:00', 'price' => 380000],
            ['bus_id' => 2, 'company_route_id' => 3, 'start_time' => '18:00', 'end_time' => '06:00', 'price' => 450000],
            ['bus_id' => 3, 'company_route_id' => 3, 'start_time' => '18:00', 'end_time' => '06:00', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 3, 'start_time' => '18:00', 'end_time' => '06:00', 'price' => 750000],

            // Tuyến 4: Hà Nội - Đà Nẵng
            ['bus_id' => 1, 'company_route_id' => 4, 'start_time' => '18:00', 'end_time' => '09:00', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 4, 'start_time' => '18:00', 'end_time' => '09:00', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 4, 'start_time' => '18:00', 'end_time' => '09:00', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 4, 'start_time' => '18:00', 'end_time' => '09:00', 'price' => 1050000],

            // Tuyến 5: Hà Nội - Hà Giang
            ['bus_id' => 6, 'company_route_id' => 5, 'start_time' => '06:30', 'end_time' => '12:30', 'price' => 370000],
            ['bus_id' => 6, 'company_route_id' => 5, 'start_time' => '15:30', 'end_time' => '21:30', 'price' => 370000],
            ['bus_id' => 1, 'company_route_id' => 5, 'start_time' => '20:00', 'end_time' => '02:00', 'price' => 320000],
            ['bus_id' => 2, 'company_route_id' => 5, 'start_time' => '09:00', 'end_time' => '15:00', 'price' => 370000],
            ['bus_id' => 2, 'company_route_id' => 5, 'start_time' => '19:30', 'end_time' => '01:30', 'price' => 370000],
            ['bus_id' => 3, 'company_route_id' => 5, 'start_time' => '09:00', 'end_time' => '15:00', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 5, 'start_time' => '11:30', 'end_time' => '17:30', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 5, 'start_time' => '19:30', 'end_time' => '01:30', 'price' => 420000],
            ['bus_id' => 4, 'company_route_id' => 5, 'start_time' => '09:00', 'end_time' => '15:00', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 5, 'start_time' => '11:30', 'end_time' => '17:30', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 5, 'start_time' => '19:30', 'end_time' => '01:30', 'price' => 600000],

            // Tuyến 6: Hà Giang - Hà Nội
            ['bus_id' => 6, 'company_route_id' => 6, 'start_time' => '06:30', 'end_time' => '12:30', 'price' => 370000],
            ['bus_id' => 6, 'company_route_id' => 6, 'start_time' => '15:30', 'end_time' => '21:30', 'price' => 370000],
            ['bus_id' => 1, 'company_route_id' => 6, 'start_time' => '20:30', 'end_time' => '02:30', 'price' => 320000],
            ['bus_id' => 2, 'company_route_id' => 6, 'start_time' => '09:00', 'end_time' => '15:00', 'price' => 370000],
            ['bus_id' => 2, 'company_route_id' => 6, 'start_time' => '18:00', 'end_time' => '00:00', 'price' => 370000],
            ['bus_id' => 3, 'company_route_id' => 6, 'start_time' => '07:00', 'end_time' => '13:00', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 6, 'start_time' => '09:00', 'end_time' => '15:00', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 6, 'start_time' => '11:30', 'end_time' => '17:30', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 6, 'start_time' => '15:30', 'end_time' => '21:30', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 6, 'start_time' => '17:30', 'end_time' => '23:30', 'price' => 420000],
            ['bus_id' => 3, 'company_route_id' => 6, 'start_time' => '19:30', 'end_time' => '01:30', 'price' => 420000],
            ['bus_id' => 4, 'company_route_id' => 6, 'start_time' => '07:00', 'end_time' => '13:00', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 6, 'start_time' => '09:00', 'end_time' => '15:00', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 6, 'start_time' => '11:30', 'end_time' => '17:30', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 6, 'start_time' => '15:30', 'end_time' => '21:30', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 6, 'start_time' => '17:30', 'end_time' => '23:30', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 6, 'start_time' => '19:30', 'end_time' => '01:30', 'price' => 600000],

            // Tuyến 7: Hà Nội - Ninh Bình
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '06:10', 'end_time' => '08:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '07:10', 'end_time' => '09:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '08:10', 'end_time' => '10:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '09:10', 'end_time' => '11:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '10:10', 'end_time' => '12:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '11:10', 'end_time' => '13:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '12:10', 'end_time' => '14:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '13:10', 'end_time' => '15:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '14:10', 'end_time' => '16:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '15:10', 'end_time' => '17:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '16:10', 'end_time' => '18:10', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 7, 'start_time' => '17:10', 'end_time' => '19:10', 'price' => 250000],

            // Tuyến 8: Ninh Bình - Hà Nội
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '06:30', 'end_time' => '08:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '07:30', 'end_time' => '09:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '08:30', 'end_time' => '10:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '09:30', 'end_time' => '11:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '10:30', 'end_time' => '12:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '11:30', 'end_time' => '13:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '12:30', 'end_time' => '14:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '13:30', 'end_time' => '15:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '14:30', 'end_time' => '16:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '15:30', 'end_time' => '17:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '16:30', 'end_time' => '18:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 8, 'start_time' => '17:30', 'end_time' => '19:30', 'price' => 250000],

            // Tuyến 9: Hà Giang - Sapa
            ['bus_id' => 6, 'company_route_id' => 9, 'start_time' => '09:00', 'end_time' => '16:00', 'price' => 370000],
            ['bus_id' => 2, 'company_route_id' => 9, 'start_time' => '18:00', 'end_time' => '01:00', 'price' => 370000],
            ['bus_id' => 3, 'company_route_id' => 9, 'start_time' => '18:00', 'end_time' => '01:00', 'price' => 420000],
            ['bus_id' => 4, 'company_route_id' => 9, 'start_time' => '18:00', 'end_time' => '01:00', 'price' => 600000],

            // Tuyến 10: Sapa - Hà Giang
            ['bus_id' => 2, 'company_route_id' => 10, 'start_time' => '11:00', 'end_time' => '18:00', 'price' => 370000],
            ['bus_id' => 3, 'company_route_id' => 10, 'start_time' => '11:00', 'end_time' => '18:00', 'price' => 420000],
            ['bus_id' => 4, 'company_route_id' => 10, 'start_time' => '11:00', 'end_time' => '18:00', 'price' => 600000],
            ['bus_id' => 6, 'company_route_id' => 10, 'start_time' => '17:00', 'end_time' => '00:00', 'price' => 370000],

            // Tuyến 11: Hà Nội - Cát Bà
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '06:00', 'end_time' => '09:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '07:30', 'end_time' => '11:00', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '09:00', 'end_time' => '12:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '11:00', 'end_time' => '14:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '12:30', 'end_time' => '16:00', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '14:00', 'end_time' => '17:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 11, 'start_time' => '15:30', 'end_time' => '19:00', 'price' => 270000],
            ['bus_id' => 6, 'company_route_id' => 11, 'start_time' => '06:30', 'end_time' => '10:00', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 11, 'start_time' => '08:00', 'end_time' => '11:30', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 11, 'start_time' => '10:30', 'end_time' => '14:00', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 11, 'start_time' => '14:30', 'end_time' => '18:00', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 11, 'start_time' => '16:00', 'end_time' => '19:30', 'price' => 320000],

            // Tuyến 12: Cát Bà - Hà Nội
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '07:00', 'end_time' => '10:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '09:00', 'end_time' => '12:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '11:00', 'end_time' => '14:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '12:30', 'end_time' => '16:00', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '14:00', 'end_time' => '17:30', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '15:30', 'end_time' => '19:00', 'price' => 270000],
            ['bus_id' => 5, 'company_route_id' => 12, 'start_time' => '17:00', 'end_time' => '20:30', 'price' => 270000],
            ['bus_id' => 6, 'company_route_id' => 12, 'start_time' => '05:00', 'end_time' => '08:30', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 12, 'start_time' => '08:00', 'end_time' => '11:30', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 12, 'start_time' => '10:30', 'end_time' => '14:00', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 12, 'start_time' => '14:30', 'end_time' => '18:00', 'price' => 320000],
            ['bus_id' => 6, 'company_route_id' => 12, 'start_time' => '17:00', 'end_time' => '20:30', 'price' => 320000],

            // Tuyến 13: Hà Nội - Tuần Châu
            ['bus_id' => 5, 'company_route_id' => 13, 'start_time' => '07:00', 'end_time' => '10:30', 'price' => 200000],
            ['bus_id' => 5, 'company_route_id' => 13, 'start_time' => '08:00', 'end_time' => '11:30', 'price' => 200000],
            ['bus_id' => 6, 'company_route_id' => 13, 'start_time' => '07:00', 'end_time' => '10:30', 'price' => 250000],
            ['bus_id' => 6, 'company_route_id' => 13, 'start_time' => '08:00', 'end_time' => '11:30', 'price' => 250000],

            // Tuyến 14: Tuần Châu - Hà Nội
            ['bus_id' => 5, 'company_route_id' => 14, 'start_time' => '11:30', 'end_time' => '15:00', 'price' => 200000],
            ['bus_id' => 6, 'company_route_id' => 14, 'start_time' => '11:30', 'end_time' => '15:00', 'price' => 250000],

            // Tuyến 15: Hà Nội - Mai Châu
            ['bus_id' => 6, 'company_route_id' => 15, 'start_time' => '07:00', 'end_time' => '10:30', 'price' => 300000],
            ['bus_id' => 6, 'company_route_id' => 15, 'start_time' => '12:00', 'end_time' => '15:30', 'price' => 300000],
            ['bus_id' => 6, 'company_route_id' => 15, 'start_time' => '16:00', 'end_time' => '19:30', 'price' => 300000],

            // Tuyến 16: Mai Châu - Hà Nội
            ['bus_id' => 6, 'company_route_id' => 16, 'start_time' => '04:00', 'end_time' => '07:30', 'price' => 300000],
            ['bus_id' => 6, 'company_route_id' => 16, 'start_time' => '08:30', 'end_time' => '12:00', 'price' => 300000],
            ['bus_id' => 6, 'company_route_id' => 16, 'start_time' => '13:00', 'end_time' => '16:30', 'price' => 300000],

            // Tuyến 17: Huế - Hà Nội
            ['bus_id' => 2, 'company_route_id' => 17, 'start_time' => '15:00', 'end_time' => '03:00', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 17, 'start_time' => '17:00', 'end_time' => '05:00', 'price' => 450000],
            ['bus_id' => 1, 'company_route_id' => 17, 'start_time' => '17:00', 'end_time' => '05:00', 'price' => 380000],
            ['bus_id' => 3, 'company_route_id' => 17, 'start_time' => '17:00', 'end_time' => '05:00', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 17, 'start_time' => '17:00', 'end_time' => '05:00', 'price' => 750000],

            // Tuyến 18: Đà Nẵng - Hà Nội
            ['bus_id' => 1, 'company_route_id' => 18, 'start_time' => '14:15', 'end_time' => '05:15', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 18, 'start_time' => '14:15', 'end_time' => '05:15', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 18, 'start_time' => '14:15', 'end_time' => '05:15', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 18, 'start_time' => '14:15', 'end_time' => '05:15', 'price' => 1050000],

            // Tuyến 19: Hà Nội - Phong Nha
            ['bus_id' => 1, 'company_route_id' => 19, 'start_time' => '18:00', 'end_time' => '03:00', 'price' => 380000],
            ['bus_id' => 2, 'company_route_id' => 19, 'start_time' => '18:00', 'end_time' => '03:00', 'price' => 450000],
            ['bus_id' => 3, 'company_route_id' => 19, 'start_time' => '18:00', 'end_time' => '03:00', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 19, 'start_time' => '18:00', 'end_time' => '03:00', 'price' => 750000],

            // Tuyến 20: Phong Nha - Hà Nội
            ['bus_id' => 2, 'company_route_id' => 20, 'start_time' => '20:00', 'end_time' => '05:00', 'price' => 250000],
            ['bus_id' => 2, 'company_route_id' => 20, 'start_time' => '21:30', 'end_time' => '06:30', 'price' => 450000],
            ['bus_id' => 1, 'company_route_id' => 20, 'start_time' => '21:30', 'end_time' => '06:30', 'price' => 380000],
            ['bus_id' => 3, 'company_route_id' => 20, 'start_time' => '21:30', 'end_time' => '06:30', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 20, 'start_time' => '21:30', 'end_time' => '06:30', 'price' => 750000],

            // Tuyến 21: Hà Nội - Hội An
            ['bus_id' => 1, 'company_route_id' => 21, 'start_time' => '18:00', 'end_time' => '10:00', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 21, 'start_time' => '18:00', 'end_time' => '10:00', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 21, 'start_time' => '18:00', 'end_time' => '10:00', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 21, 'start_time' => '18:00', 'end_time' => '10:00', 'price' => 1050000],

            // Tuyến 22: Hội An - Hà Nội
            ['bus_id' => 1, 'company_route_id' => 22, 'start_time' => '13:30', 'end_time' => '05:30', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 22, 'start_time' => '13:30', 'end_time' => '05:30', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 22, 'start_time' => '13:30', 'end_time' => '05:30', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 22, 'start_time' => '13:30', 'end_time' => '05:30', 'price' => 1050000],

            // Tuyến 23: Ninh Bình - Phong Nha
            ['bus_id' => 1, 'company_route_id' => 23, 'start_time' => '20:30', 'end_time' => '03:30', 'price' => 380000],
            ['bus_id' => 2, 'company_route_id' => 23, 'start_time' => '20:30', 'end_time' => '03:30', 'price' => 450000],
            ['bus_id' => 3, 'company_route_id' => 23, 'start_time' => '20:30', 'end_time' => '03:30', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 23, 'start_time' => '20:30', 'end_time' => '03:30', 'price' => 750000],

            // Tuyến 24: Phong Nha - Ninh Bình
            ['bus_id' => 2, 'company_route_id' => 24, 'start_time' => '20:00', 'end_time' => '03:00', 'price' => 250000],
            ['bus_id' => 2, 'company_route_id' => 24, 'start_time' => '21:30', 'end_time' => '04:30', 'price' => 450000],
            ['bus_id' => 1, 'company_route_id' => 24, 'start_time' => '21:30', 'end_time' => '04:30', 'price' => 380000],
            ['bus_id' => 3, 'company_route_id' => 24, 'start_time' => '21:30', 'end_time' => '04:30', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 24, 'start_time' => '21:30', 'end_time' => '04:30', 'price' => 750000],

            // Tuyến 25: Ninh Bình - Huế
            ['bus_id' => 1, 'company_route_id' => 25, 'start_time' => '20:30', 'end_time' => '06:30', 'price' => 380000],
            ['bus_id' => 2, 'company_route_id' => 25, 'start_time' => '20:30', 'end_time' => '06:30', 'price' => 450000],
            ['bus_id' => 3, 'company_route_id' => 25, 'start_time' => '20:30', 'end_time' => '06:30', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 25, 'start_time' => '20:30', 'end_time' => '06:30', 'price' => 750000],

            // Tuyến 26: Huế - Ninh Bình
            ['bus_id' => 2, 'company_route_id' => 26, 'start_time' => '15:00', 'end_time' => '01:00', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 26, 'start_time' => '17:00', 'end_time' => '03:00', 'price' => 450000],
            ['bus_id' => 1, 'company_route_id' => 26, 'start_time' => '17:00', 'end_time' => '03:00', 'price' => 380000],
            ['bus_id' => 3, 'company_route_id' => 26, 'start_time' => '17:00', 'end_time' => '03:00', 'price' => 550000],
            ['bus_id' => 4, 'company_route_id' => 26, 'start_time' => '17:00', 'end_time' => '03:00', 'price' => 750000],

            // Tuyến 27: Ninh Bình - Đà Nẵng
            ['bus_id' => 1, 'company_route_id' => 27, 'start_time' => '20:30', 'end_time' => '09:30', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 27, 'start_time' => '20:30', 'end_time' => '09:30', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 27, 'start_time' => '20:30', 'end_time' => '09:30', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 27, 'start_time' => '20:30', 'end_time' => '09:30', 'price' => 1050000],

            // Tuyến 28: Đà Nẵng - Ninh Bình
            ['bus_id' => 1, 'company_route_id' => 28, 'start_time' => '14:15', 'end_time' => '03:15', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 28, 'start_time' => '14:15', 'end_time' => '03:15', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 28, 'start_time' => '14:15', 'end_time' => '03:15', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 28, 'start_time' => '14:15', 'end_time' => '03:15', 'price' => 1050000],

            // Tuyến 29: Ninh Bình - Hội An
            ['bus_id' => 1, 'company_route_id' => 29, 'start_time' => '20:30', 'end_time' => '10:30', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 29, 'start_time' => '20:30', 'end_time' => '10:30', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 29, 'start_time' => '20:30', 'end_time' => '10:30', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 29, 'start_time' => '20:30', 'end_time' => '10:30', 'price' => 1050000],

            // Tuyến 30: Hội An - Ninh Bình
            ['bus_id' => 1, 'company_route_id' => 30, 'start_time' => '13:30', 'end_time' => '03:30', 'price' => 450000],
            ['bus_id' => 2, 'company_route_id' => 30, 'start_time' => '13:30', 'end_time' => '03:30', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 30, 'start_time' => '13:30', 'end_time' => '03:30', 'price' => 750000],
            ['bus_id' => 4, 'company_route_id' => 30, 'start_time' => '13:30', 'end_time' => '03:30', 'price' => 1050000],

            // Tuyến 31: Đà Nẵng - Hội An
            ['bus_id' => 1, 'company_route_id' => 31, 'start_time' => '11:00', 'end_time' => '12:00', 'price' => 150000],
            ['bus_id' => 2, 'company_route_id' => 31, 'start_time' => '11:00', 'end_time' => '12:00', 'price' => 200000],

            // Tuyến 32: Hội An - Đà Nẵng
            ['bus_id' => 1, 'company_route_id' => 32, 'start_time' => '13:30', 'end_time' => '14:30', 'price' => 150000],
            ['bus_id' => 2, 'company_route_id' => 32, 'start_time' => '13:30', 'end_time' => '14:30', 'price' => 200000],

            // Tuyến 33: Huế - Hội An
            ['bus_id' => 1, 'company_route_id' => 33, 'start_time' => '07:30', 'end_time' => '11:30', 'price' => 200000],
            ['bus_id' => 2, 'company_route_id' => 33, 'start_time' => '07:30', 'end_time' => '11:30', 'price' => 250000],

            // Tuyến 34: Hội An - Huế
            ['bus_id' => 1, 'company_route_id' => 34, 'start_time' => '13:30', 'end_time' => '17:30', 'price' => 200000],
            ['bus_id' => 2, 'company_route_id' => 34, 'start_time' => '13:30', 'end_time' => '17:30', 'price' => 250000],

            // Tuyến 35: Huế - Đà Nẵng
            ['bus_id' => 1, 'company_route_id' => 35, 'start_time' => '07:30', 'end_time' => '10:30', 'price' => 200000],
            ['bus_id' => 2, 'company_route_id' => 35, 'start_time' => '07:30', 'end_time' => '10:30', 'price' => 250000],

            // Tuyến 36: Đà Nẵng - Huế
            ['bus_id' => 1, 'company_route_id' => 36, 'start_time' => '14:15', 'end_time' => '17:15', 'price' => 200000],
            ['bus_id' => 2, 'company_route_id' => 36, 'start_time' => '14:15', 'end_time' => '17:15', 'price' => 250000],

            // Tuyến 37: Phong Nha - Hội An
            ['bus_id' => 1, 'company_route_id' => 37, 'start_time' => '04:00', 'end_time' => '11:00', 'price' => 300000],
            ['bus_id' => 2, 'company_route_id' => 37, 'start_time' => '04:00', 'end_time' => '11:00', 'price' => 400000],

            // Tuyến 38: Hội An - Phong Nha
            ['bus_id' => 1, 'company_route_id' => 38, 'start_time' => '13:30', 'end_time' => '20:30', 'price' => 300000],
            ['bus_id' => 2, 'company_route_id' => 38, 'start_time' => '13:30', 'end_time' => '20:30', 'price' => 400000],

            // Tuyến 39: Phong Nha - Đà Nẵng
            ['bus_id' => 1, 'company_route_id' => 39, 'start_time' => '04:00', 'end_time' => '10:00', 'price' => 300000],
            ['bus_id' => 2, 'company_route_id' => 39, 'start_time' => '04:00', 'end_time' => '10:00', 'price' => 400000],

            // Tuyến 40: Đà Nẵng - Phong Nha
            ['bus_id' => 1, 'company_route_id' => 40, 'start_time' => '14:15', 'end_time' => '20:15', 'price' => 300000],
            ['bus_id' => 2, 'company_route_id' => 40, 'start_time' => '14:15', 'end_time' => '20:15', 'price' => 400000],

            // Tuyến 41: Phong Nha - Huế
            ['bus_id' => 1, 'company_route_id' => 41, 'start_time' => '04:00', 'end_time' => '07:30', 'price' => 250000],
            ['bus_id' => 2, 'company_route_id' => 41, 'start_time' => '04:00', 'end_time' => '07:30', 'price' => 300000],

            // Tuyến 42: Huế - Phong Nha
            ['bus_id' => 1, 'company_route_id' => 42, 'start_time' => '17:00', 'end_time' => '20:30', 'price' => 250000],
            ['bus_id' => 2, 'company_route_id' => 42, 'start_time' => '17:00', 'end_time' => '20:30', 'price' => 300000],

            // Tuyến 43: Hà Giang - Ninh Bình
            ['bus_id' => 1, 'company_route_id' => 43, 'start_time' => '20:00', 'end_time' => '04:00', 'price' => 370000],
            ['bus_id' => 3, 'company_route_id' => 43, 'start_time' => '20:00', 'end_time' => '04:00', 'price' => 460000],
            ['bus_id' => 4, 'company_route_id' => 43, 'start_time' => '20:00', 'end_time' => '04:00', 'price' => 640000],

            // Tuyến 44: Ninh Bình - Hà Giang
            ['bus_id' => 1, 'company_route_id' => 44, 'start_time' => '18:00', 'end_time' => '02:00', 'price' => 370000],
            ['bus_id' => 3, 'company_route_id' => 44, 'start_time' => '19:00', 'end_time' => '03:00', 'price' => 460000],
            ['bus_id' => 4, 'company_route_id' => 44, 'start_time' => '19:00', 'end_time' => '03:00', 'price' => 640000],

            // Tuyến 45: Hà Giang - Cát Bà
            ['bus_id' => 2, 'company_route_id' => 45, 'start_time' => '19:00', 'end_time' => '07:00', 'price' => 550000],
            ['bus_id' => 3, 'company_route_id' => 45, 'start_time' => '19:00', 'end_time' => '07:00', 'price' => 600000],
            ['bus_id' => 4, 'company_route_id' => 45, 'start_time' => '19:00', 'end_time' => '07:00', 'price' => 800000],

            // Tuyến 46: Cát Bà - Hà Giang
            ['bus_id' => 2, 'company_route_id' => 46, 'start_time' => '17:00', 'end_time' => '05:00', 'price' => 550000],
        ];

        $insertData = [];
        foreach ($data as $item) {
            $insertData[] = array_merge($item, [
                'is_active' => 1,
                'priority' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('bus_routes')->insert($insertData);
    }


    private function seedMenus(): void
    {
        DB::table('menus')->insert([
            ['id' => 1, 'name' => 'Trang chủ', 'url' => '/', 'parent_id' => null, 'priority' => 0, 'type' => 'custom_link', 'related_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'Tuyến đường', 'url' => '#', 'parent_id' => null, 'priority' => 1, 'type' => 'custom_link', 'related_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'name' => 'Liên Hệ', 'url' => '/lien-he', 'parent_id' => null, 'priority' => 2, 'type' => 'custom_link', 'related_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 4, 'name' => 'Hà Nội - Sapa', 'url' => '/tuyen-duong/ha-noi-sapa', 'parent_id' => 2, 'priority' => 0, 'type' => 'route', 'related_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 5, 'name' => 'Sapa - Hà Nội', 'url' => '/tuyen-duong/sapa-ha-noi', 'parent_id' => 2, 'priority' => 1, 'type' => 'route', 'related_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 6, 'name' => 'Hà Nội - Huế', 'url' => '/tuyen-duong/ha-noi-hue', 'parent_id' => 2, 'priority' => 2, 'type' => 'route', 'related_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 7, 'name' => 'Hà Nội - Đà Nẵng', 'url' => '/tuyen-duong/ha-noi-da-nang', 'parent_id' => 2, 'priority' => 3, 'type' => 'route', 'related_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id' => 8, 'name' => 'Hà Nội - Hà Giang', 'url' => '/tuyen-duong/ha-noi-ha-giang', 'parent_id' => 2, 'priority' => 4, 'type' => 'route', 'related_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedBookings(): void
    {
        DB::table('bookings')->insert([
            [
                'booking_code' => 'KEB' . Str::upper(Str::random(8)),
                'user_id' => 2,
                'bus_route_id' => 1,
                'booking_date' => now()->addDays(10)->toDateString(),
                'customer_name' => 'Nguyễn Văn An',
                'customer_email' => 'nguyenvanan@gmail.com',
                'customer_phone' => '0123456789',
                'pickup_stop_id' => 1,
                'dropoff_stop_id' => 2,
                'quantity' => 2,
                'total_price' => 540000,
                'status' => 'confirmed',
                'payment_method' => 'online_banking',
                'payment_status' => 'paid',
                'notes' => 'Ghi chú cho đơn hàng mẫu 1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'booking_code' => 'KEB' . Str::upper(Str::random(8)),
                'user_id' => 3,
                'bus_route_id' => 10,
                'booking_date' => now()->addDays(20)->toDateString(),
                'customer_name' => 'Trần Thị Bình',
                'customer_email' => 'tranthibinh@gmail.com',
                'customer_phone' => '0987123456',
                'pickup_stop_id' => 2,
                'dropoff_stop_id' => 1,
                'quantity' => 1,
                'total_price' => 400000,
                'status' => 'pending',
                'payment_method' => 'cash_on_pickup',
                'payment_status' => 'unpaid',
                'notes' => 'Ghi chú cho đơn hàng mẫu 2',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'booking_code' => 'KEB' . Str::upper(Str::random(8)),
                'user_id' => null,
                'bus_route_id' => 1,
                'booking_date' => now()->addDays(5)->toDateString(),
                'customer_name' => 'Minh Long',
                'customer_email' => 'deocomate@gmail.com',
                'customer_phone' => '0999888777',
                'pickup_stop_id' => null,
                'dropoff_stop_id' => 2,
                'quantity' => 1,
                'total_price' => 270000,
                'status' => 'confirmed',
                'payment_method' => 'cash_on_pickup',
                'payment_status' => 'unpaid',
                'notes' => '[Đón tại khách sạn]: Khách sạn ABC, 123 Phố Cổ, Hà Nội' . PHP_EOL . '[Ghi chú của khách]: Vui lòng gọi trước 30 phút.',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
