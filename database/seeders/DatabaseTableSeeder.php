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
            // Bảng con trước
            'bookings',
            'company_route_stops',
            'bus_routes',
            'buses',
            'company_routes',
            'companies',
            'stops',
            'districts',
            'routes',
            'menus',
            // Bảng cha sau
            'users',
            'provinces',
            'district_types',
            // Các bảng khác
            'web_profiles',
            'bus_services',
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
                'email_verified_at' => '2025-10-03 06:27:39',
                'password' => '$2y$12$MwPM20HI4V1o5viry.II7ewdXUhwkFBwIcHKOVLpuAHKBwlsQeRty', // admin
                'role' => 'admin',
                'remember_token' => 'BUy9mtpx1cCed9SX5JHQFNlBKr6jNsVK19cOgUWWGuMbBYtRRXLaR3uTo8tS',
                'created_at' => '2025-10-03 06:27:39',
                'updated_at' => '2025-10-03 06:27:39',
            ],
            [
                'id' => 2,
                'name' => 'Nguyễn Văn An',
                'email' => 'nguyenvanan@gmail.com',
                'phone' => '0123456789',
                'address' => '123 Đường Cầu Giấy, Hà Nội',
                'email_verified_at' => '2025-10-03 06:27:39',
                'password' => '$2y$12$nJfIwnzcs4EFTRyBaC/8WONBMLaYtYm2FbJKR2rYIB7YDYocksQk2', // password
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => '2025-10-03 06:27:40',
                'updated_at' => '2025-10-03 06:27:40',
            ],
            [
                'id' => 3,
                'name' => 'Trần Thị Bình',
                'email' => 'tranthibinh@gmail.com',
                'phone' => '0987123456',
                'address' => '456 Đường Lê Lợi, Quận 1, TP. Hồ Chí Minh',
                'email_verified_at' => '2025-10-03 06:27:40',
                'password' => '$2y$12$vaaAKGAqBp1hmIIFn1jbo.Oixfx3hqaPRvr11k6459cORu5YEpgD6', // password
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => '2025-10-03 06:27:40',
                'updated_at' => '2025-10-03 06:27:40',
            ],
            [
                'id' => 4,
                'name' => 'Lê Hoàng Cường',
                'email' => 'lehoangcuong@gmail.com',
                'phone' => '0369852147',
                'address' => '789 Đường Nguyễn Văn Linh, Đà Nẵng',
                'email_verified_at' => '2025-10-03 06:27:40',
                'password' => '$2y$12$VqlMLK/CO0QtFHk/s/.DK.cQ5JwA/oNzfWbw7tTudNk5tUM1uZ6eG', // password
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => '2025-10-03 06:27:40',
                'updated_at' => '2025-10-03 06:27:40',
            ],
            [
                'id' => 5,
                'name' => 'Minh Long',
                'email' => 'deocomate@gmail.com',
                'phone' => '0565651189',
                'address' => 'Vé test',
                'email_verified_at' => null,
                'password' => null,
                'role' => 'guest',
                'remember_token' => null,
                'created_at' => '2025-10-03 06:27:40',
                'updated_at' => '2025-10-03 06:27:40',
            ],
            [
                'id' => 6,
                'name' => 'King Express Bus',
                'email' => 'company@kingexpressbus.com',
                'phone' => '0924300366',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'email_verified_at' => '2025-10-03 06:27:40',
                'password' => '$2y$12$7TB0yStqTMIcXC4xV7GyzuyPYB8nFX6ysmK5.Cmf5agcTBg0MBtoK', // admin
                'role' => 'company',
                'remember_token' => 'Gdhngtvgxtuo38MP8M4bdBmrU3Tx4F6LZ0nvhKm0SRNAfXKLZJYT0picyUVM',
                'created_at' => '2025-10-03 06:27:41',
                'updated_at' => '2025-10-10 03:19:01',
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
                'created_at' => '2025-10-03 06:27:41',
                'updated_at' => '2025-10-10 03:18:33',
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

    private function seedProvinces(): void
    {
        DB::table('provinces')->insert([
            ['id' => 1, 'name' => 'Hà Nội', 'slug' => 'ha-noi', 'title' => 'Vé xe khách đi Hà Nội', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Hà Nội và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 100, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'name' => 'Sapa', 'slug' => 'sapa', 'title' => 'Vé xe khách đi Sapa', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Sapa và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/sapa.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/sapa.jpg"]', 'content' => null, 'priority' => 99, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'name' => 'Tuần Châu', 'slug' => 'tuan-chau', 'title' => 'Vé xe khách đi Tuần Châu', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Tuần Châu và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/tuan-chau.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/tuan-chau.jpg"]', 'content' => null, 'priority' => 5, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'name' => 'Mai Châu', 'slug' => 'mai-chau', 'title' => 'Vé xe khách đi Mai Châu', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Mai Châu và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/mai-chau.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/mai-chau.jpg"]', 'content' => null, 'priority' => 6, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 5, 'name' => 'Cát Bà', 'slug' => 'cat-ba', 'title' => 'Vé xe khách đi Cát Bà', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Cát Bà và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/cat-ba.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/cat-ba.jpg"]', 'content' => null, 'priority' => 7, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 6, 'name' => 'Hà Giang', 'slug' => 'ha-giang', 'title' => 'Vé xe khách đi Hà Giang', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Hà Giang và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/ha-giang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-giang.jpg"]', 'content' => null, 'priority' => 8, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 7, 'name' => 'Ninh Bình', 'slug' => 'ninh-binh', 'title' => 'Vé xe khách đi Ninh Bình', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Ninh Bình và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 9, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 8, 'name' => 'Phong Nha', 'slug' => 'phong-nha', 'title' => 'Vé xe khách đi Phong Nha', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Phong Nha và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/phong-nha.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/phong-nha.jpg"]', 'content' => null, 'priority' => 10, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 9, 'name' => 'Huế', 'slug' => 'hue', 'title' => 'Vé xe khách đi Huế', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Huế và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/hue.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hue.jpg"]', 'content' => null, 'priority' => 98, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 10, 'name' => 'Đà Nẵng', 'slug' => 'da-nang', 'title' => 'Vé xe khách đi Đà Nẵng', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Đà Nẵng và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/da-nang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/da-nang.jpg"]', 'content' => null, 'priority' => 97, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 11, 'name' => 'Hội An', 'slug' => 'hoi-an', 'title' => 'Vé xe khách đi Hội An', 'description' => 'Đặt vé xe giường nằm, limousine chất lượng cao đi Hội An và các tỉnh.', 'thumbnail_url' => '/userfiles/files/city_imgs/hoi-an.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hoi-an.jpg"]', 'content' => null, 'priority' => 11, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
        ]);
    }

    private function seedDistricts(): void
    {
        DB::table('districts')->insert([
            ['id' => 1, 'province_id' => 1, 'district_type_id' => 6, 'name' => 'Thành Phố Hà Nội', 'slug' => 'thanh-pho-ha-noi', 'title' => 'Thành Phố Hà Nội', 'description' => 'Các điểm đón trả tại Thành Phố Hà Nội', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'province_id' => 2, 'district_type_id' => 6, 'name' => 'Thành Phố Sapa', 'slug' => 'thanh-pho-sapa', 'title' => 'Thành Phố Sapa', 'description' => 'Các điểm đón trả tại Thành Phố Sapa', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'province_id' => 3, 'district_type_id' => 6, 'name' => 'Thành Phố Tuần Châu', 'slug' => 'thanh-pho-tuan-chau', 'title' => 'Thành Phố Tuần Châu', 'description' => 'Các điểm đón trả tại Thành Phố Tuần Châu', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'province_id' => 4, 'district_type_id' => 6, 'name' => 'Thành Phố Mai Châu', 'slug' => 'thanh-pho-mai-chau', 'title' => 'Thành Phố Mai Châu', 'description' => 'Các điểm đón trả tại Thành Phố Mai Châu', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 5, 'province_id' => 5, 'district_type_id' => 6, 'name' => 'Thành Phố Cát Bà', 'slug' => 'thanh-pho-cat-ba', 'title' => 'Thành Phố Cát Bà', 'description' => 'Các điểm đón trả tại Thành Phố Cát Bà', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 6, 'province_id' => 6, 'district_type_id' => 6, 'name' => 'Thành Phố Hà Giang', 'slug' => 'thanh-pho-ha-giang', 'title' => 'Thành Phố Hà Giang', 'description' => 'Các điểm đón trả tại Thành Phố Hà Giang', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 7, 'province_id' => 7, 'district_type_id' => 6, 'name' => 'Thành Phố Ninh Bình', 'slug' => 'thanh-pho-ninh-binh', 'title' => 'Thành Phố Ninh Bình', 'description' => 'Các điểm đón trả tại Thành Phố Ninh Bình', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 8, 'province_id' => 8, 'district_type_id' => 6, 'name' => 'Thành Phố Phong Nha', 'slug' => 'thanh-pho-phong-nha', 'title' => 'Thành Phố Phong Nha', 'description' => 'Các điểm đón trả tại Thành Phố Phong Nha', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 9, 'province_id' => 9, 'district_type_id' => 6, 'name' => 'Thành Phố Huế', 'slug' => 'thanh-pho-hue', 'title' => 'Thành Phố Huế', 'description' => 'Các điểm đón trả tại Thành Phố Huế', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 10, 'province_id' => 10, 'district_type_id' => 6, 'name' => 'Thành Phố Đà Nẵng', 'slug' => 'thanh-pho-da-nang', 'title' => 'Thành Phố Đà Nẵng', 'description' => 'Các điểm đón trả tại Thành Phố Đà Nẵng', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 11, 'province_id' => 11, 'district_type_id' => 6, 'name' => 'Thành Phố Hội An', 'slug' => 'thanh-pho-hoi-an', 'title' => 'Thành Phố Hội An', 'description' => 'Các điểm đón trả tại Thành Phố Hội An', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
        ]);
    }

    private function seedStops(): void
    {
        DB::table('stops')->insert([
            ['id' => 1, 'district_id' => 1, 'name' => 'VP Hà Nội', 'address' => '19 Hàng Thiếc', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'district_id' => 2, 'name' => 'VP Sapa', 'address' => '458 Dien Bien Phu', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'district_id' => 3, 'name' => 'VP Tuần Châu', 'address' => 'Tuần Châu Harbor', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'district_id' => 4, 'name' => 'VP Mai Châu', 'address' => 'Hotel', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 5, 'district_id' => 5, 'name' => 'VP Cát Bà', 'address' => '217 Mot Thang Tu', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 6, 'district_id' => 6, 'name' => 'VP Hà Giang', 'address' => '100 Tran Phu', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 7, 'district_id' => 7, 'name' => 'VP Ninh Bình', 'address' => 'No 2a, đường 27/7 - TP Ninh Bình', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 8, 'district_id' => 8, 'name' => 'VP Phong Nha', 'address' => 'Central Backpacker Phong Nha', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 9, 'district_id' => 9, 'name' => 'VP Huế', 'address' => '07 Đội Cung- TP Huế', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 10, 'district_id' => 10, 'name' => 'VP Đà Nẵng', 'address' => 'Số 28 đường 3/2- TP Đà Nẵng', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 11, 'district_id' => 11, 'name' => 'VP Hội An', 'address' => '105 Tôn Đức Thắng - TP Hội An', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 12, 'district_id' => 7, 'name' => 'VP Tam Cốc', 'address' => 'Travel Agency- Tam Cốc (NEW PICK UP POINT)', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 13, 'district_id' => 7, 'name' => 'BX Đồng Gừng', 'address' => 'Đồng Gừng Bus Station', 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
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
                'image_list_url' => null,
                'content' => '<p>Giới thiệu về nhà xe King Express Bus.</p>',
                'phone' => '0924300366',
                'hotline' => null,
                'email' => 'kingexpressbus@gmail.com',
                'address' => '19 Hàng Thiếc - Hoàn Kiếm - Hà Nội',
                'priority' => 1,
                'created_at' => '2025-10-03 06:27:41',
                'updated_at' => '2025-10-10 03:19:01',
            ]
        ]);
    }

    private function seedBuses(): void
    {
        DB::table('buses')->insert([
            ['id' => 1, 'company_id' => 1, 'name' => 'Sleeper', 'model_name' => 'Sleeper Bus', 'seat_count' => 38, 'seat_map' => null, 'services' => '["Chăn gối","Nước uống"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/sleeper/1.jpg', 'image_list_url' => '["/userfiles/files/kingexpressbus/sleeper/1.jpg","/userfiles/files/kingexpressbus/sleeper/2.jpg"]', 'content' => '<p>Xe giường nằm tiêu chuẩn là lựa chọn kinh tế cho các chuyến đi dài.</p>', 'priority' => 1, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'company_id' => 1, 'name' => 'Cabin Single', 'model_name' => 'Cabin Single', 'seat_count' => 22, 'seat_map' => null, 'services' => '["Nước uống","Rèm che","TV","Cổng sạc USB"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin/1.jpg', 'image_list_url' => '["/userfiles/files/kingexpressbus/cabin/1.jpg","/userfiles/files/kingexpressbus/cabin/2.jpg","/userfiles/files/kingexpressbus/cabin/3.jpg"]', 'content' => '<p>Cabin đơn đem lại sự riêng tư tuyệt đối với đầy đủ tiện nghi.</p>', 'priority' => 3, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'company_id' => 1, 'name' => 'Cabin Double', 'model_name' => 'Cabin Double', 'seat_count' => 22, 'seat_map' => null, 'services' => '["Chăn gối","TV","Cổng sạc USB"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin_double/1.jpg', 'image_list_url' => '["/userfiles/files/kingexpressbus/cabin_double/1.jpg","/userfiles/files/kingexpressbus/cabin_double/2.jpg","/userfiles/files/kingexpressbus/cabin_double/3.jpg"]', 'content' => '<p>Cabin đôi lý tưởng cho cặp đôi hoặc gia đình nhỏ.</p>', 'priority' => 4, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'company_id' => 1, 'name' => 'Seater', 'model_name' => 'Seater Bus', 'seat_count' => 16, 'seat_map' => null, 'services' => '["Điều hoà","Nước uống"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/seater/1.png', 'image_list_url' => '["/userfiles/files/kingexpressbus/seater/1.png","/userfiles/files/kingexpressbus/seater/2.png"]', 'content' => '<p>Xe ghế ngồi 16 chỗ phù hợp cho những chuyến đi ngắn, linh hoạt.</p>', 'priority' => 6, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 5, 'company_id' => 1, 'name' => 'Limousine', 'model_name' => 'Limousine Bus', 'seat_count' => 9, 'seat_map' => null, 'services' => '["Nước uống","Wi-Fi","Điều hoà","Ổ sạc"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/limousine/1.png', 'image_list_url' => '["/userfiles/files/kingexpressbus/limousine/1.png","/userfiles/files/kingexpressbus/limousine/2.png"]', 'content' => '<p>Xe Limousine hiện đại với ghế hạng thương gia và dịch vụ cao cấp.</p>', 'priority' => 5, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 6, 'company_id' => 1, 'name' => 'VIP 32 sleeper', 'model_name' => 'VIP Sleeper 32', 'seat_count' => 32, 'seat_map' => null, 'services' => '["Nước uống","Chăn gối","Rèm che"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-1.jpg', 'image_list_url' => '["/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-1.jpg","/userfiles/files/kingexpressbus/sleeper_vip_32/sleeper32-2.jpg"]', 'content' => '<p>Xe giường nằm VIP 32 chỗ với rèm che riêng tư, không gian sang trọng.</p>', 'priority' => 2, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 7, 'company_id' => 1, 'name' => 'VIP 22 cabin single', 'model_name' => 'VIP Cabin Single 22', 'seat_count' => 22, 'seat_map' => null, 'services' => '["Nước uống","Rèm che","TV","Cổng sạc USB"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin/1.jpg', 'image_list_url' => '["/userfiles/files/kingexpressbus/cabin/1.jpg","/userfiles/files/kingexpressbus/cabin/2.jpg","/userfiles/files/kingexpressbus/cabin/3.jpg"]', 'content' => '<p>Cabin VIP đơn 22 giường đem lại sự riêng tư tuyệt đối với đầy đủ tiện nghi.</p>', 'priority' => 3, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 8, 'company_id' => 1, 'name' => 'VIP 22 cabin double', 'model_name' => 'VIP Cabin Double 22', 'seat_count' => 22, 'seat_map' => null, 'services' => '["Chăn gối","TV","Cổng sạc USB"]', 'thumbnail_url' => '/userfiles/files/kingexpressbus/cabin_double/1.jpg', 'image_list_url' => '["/userfiles/files/kingexpressbus/cabin_double/1.jpg","/userfiles/files/kingexpressbus/cabin_double/2.jpg","/userfiles/files/kingexpressbus/cabin_double/3.jpg"]', 'content' => '<p>Cabin VIP đôi 22 giường lý tưởng cho cặp đôi hoặc gia đình nhỏ.</p>', 'priority' => 4, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
        ]);
    }

    private function seedRoutes(): void
    {
        DB::table('routes')->insert([
            ['id' => 1, 'province_start_id' => 1, 'province_end_id' => 2, 'name' => 'Hà Nội - Sapa', 'slug' => 'ha-noi-sapa', 'title' => 'Vé xe Hà Nội - Sapa', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Sapa', 'duration' => '6h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/sapa.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/sapa.jpg"]', 'content' => null, 'priority' => 1, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'province_start_id' => 2, 'province_end_id' => 1, 'name' => 'Sapa - Hà Nội', 'slug' => 'sapa-ha-noi', 'title' => 'Vé xe Sapa - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Sapa - Hà Nội', 'duration' => '6h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 2, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'province_start_id' => 1, 'province_end_id' => 9, 'name' => 'Hà Nội - Huế', 'slug' => 'ha-noi-hue', 'title' => 'Vé xe Hà Nội - Huế', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Huế', 'duration' => '12h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hue.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hue.jpg"]', 'content' => null, 'priority' => 3, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'province_start_id' => 1, 'province_end_id' => 10, 'name' => 'Hà Nội - Đà Nẵng', 'slug' => 'ha-noi-da-nang', 'title' => 'Vé xe Hà Nội - Đà Nẵng', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Đà Nẵng', 'duration' => '15h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/da-nang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/da-nang.jpg"]', 'content' => null, 'priority' => 4, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 5, 'province_start_id' => 1, 'province_end_id' => 6, 'name' => 'Hà Nội - Hà Giang', 'slug' => 'ha-noi-ha-giang', 'title' => 'Vé xe Hà Nội - Hà Giang', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Hà Giang', 'duration' => '6h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-giang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-giang.jpg"]', 'content' => null, 'priority' => 5, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 6, 'province_start_id' => 6, 'province_end_id' => 1, 'name' => 'Hà Giang - Hà Nội', 'slug' => 'ha-giang-ha-noi', 'title' => 'Vé xe Hà Giang - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Giang - Hà Nội', 'duration' => '6h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 6, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 7, 'province_start_id' => 1, 'province_end_id' => 7, 'name' => 'Hà Nội - Ninh Bình', 'slug' => 'ha-noi-ninh-binh', 'title' => 'Vé xe Hà Nội - Ninh Bình', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Ninh Bình', 'duration' => '2h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 7, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 8, 'province_start_id' => 7, 'province_end_id' => 1, 'name' => 'Ninh Bình - Hà Nội', 'slug' => 'ninh-binh-ha-noi', 'title' => 'Vé xe Ninh Bình - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Ninh Bình - Hà Nội', 'duration' => '2h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 8, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 9, 'province_start_id' => 6, 'province_end_id' => 2, 'name' => 'Hà Giang - Sapa', 'slug' => 'ha-giang-sapa', 'title' => 'Vé xe Hà Giang - Sapa', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Giang - Sapa', 'duration' => '7h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/sapa.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/sapa.jpg"]', 'content' => null, 'priority' => 9, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 10, 'province_start_id' => 2, 'province_end_id' => 6, 'name' => 'Sapa - Hà Giang', 'slug' => 'sapa-ha-giang', 'title' => 'Vé xe Sapa - Hà Giang', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Sapa - Hà Giang', 'duration' => '7h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-giang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-giang.jpg"]', 'content' => null, 'priority' => 10, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 11, 'province_start_id' => 1, 'province_end_id' => 5, 'name' => 'Hà Nội - Cát Bà', 'slug' => 'ha-noi-cat-ba', 'title' => 'Vé xe Hà Nội - Cát Bà', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Cát Bà', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/cat-ba.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/cat-ba.jpg"]', 'content' => null, 'priority' => 11, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 12, 'province_start_id' => 5, 'province_end_id' => 1, 'name' => 'Cát Bà - Hà Nội', 'slug' => 'cat-ba-ha-noi', 'title' => 'Vé xe Cát Bà - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Cát Bà - Hà Nội', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 12, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 13, 'province_start_id' => 1, 'province_end_id' => 3, 'name' => 'Hà Nội - Tuần Châu', 'slug' => 'ha-noi-tuan-chau', 'title' => 'Vé xe Hà Nội - Tuần Châu', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Tuần Châu', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/tuan-chau.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/tuan-chau.jpg"]', 'content' => null, 'priority' => 13, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 14, 'province_start_id' => 3, 'province_end_id' => 1, 'name' => 'Tuần Châu - Hà Nội', 'slug' => 'tuan-chau-ha-noi', 'title' => 'Vé xe Tuần Châu - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Tuần Châu - Hà Nội', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 14, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 15, 'province_start_id' => 1, 'province_end_id' => 4, 'name' => 'Hà Nội - Mai Châu', 'slug' => 'ha-noi-mai-chau', 'title' => 'Vé xe Hà Nội - Mai Châu', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Mai Châu', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/mai-chau.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/mai-chau.jpg"]', 'content' => null, 'priority' => 15, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 16, 'province_start_id' => 4, 'province_end_id' => 1, 'name' => 'Mai Châu - Hà Nội', 'slug' => 'mai-chau-ha-noi', 'title' => 'Vé xe Mai Châu - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Mai Châu - Hà Nội', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 16, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 17, 'province_start_id' => 9, 'province_end_id' => 1, 'name' => 'Huế - Hà Nội', 'slug' => 'hue-ha-noi', 'title' => 'Vé xe Huế - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Huế - Hà Nội', 'duration' => '12h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 17, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 18, 'province_start_id' => 10, 'province_end_id' => 1, 'name' => 'Đà Nẵng - Hà Nội', 'slug' => 'da-nang-ha-noi', 'title' => 'Vé xe Đà Nẵng - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Đà Nẵng - Hà Nội', 'duration' => '15h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 18, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 19, 'province_start_id' => 1, 'province_end_id' => 8, 'name' => 'Hà Nội - Phong Nha', 'slug' => 'ha-noi-phong-nha', 'title' => 'Vé xe Hà Nội - Phong Nha', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Phong Nha', 'duration' => '9h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/phong-nha.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/phong-nha.jpg"]', 'content' => null, 'priority' => 19, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 20, 'province_start_id' => 8, 'province_end_id' => 1, 'name' => 'Phong Nha - Hà Nội', 'slug' => 'phong-nha-ha-noi', 'title' => 'Vé xe Phong Nha - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Phong Nha - Hà Nội', 'duration' => '9h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 20, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 21, 'province_start_id' => 1, 'province_end_id' => 11, 'name' => 'Hà Nội - Hội An', 'slug' => 'ha-noi-hoi-an', 'title' => 'Vé xe Hà Nội - Hội An', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Hội An', 'duration' => '16h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hoi-an.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hoi-an.jpg"]', 'content' => null, 'priority' => 21, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 22, 'province_start_id' => 11, 'province_end_id' => 1, 'name' => 'Hội An - Hà Nội', 'slug' => 'hoi-an-ha-noi', 'title' => 'Vé xe Hội An - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hội An - Hà Nội', 'duration' => '16h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-noi.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-noi.jpg"]', 'content' => null, 'priority' => 22, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 23, 'province_start_id' => 7, 'province_end_id' => 8, 'name' => 'Ninh Bình - Phong Nha', 'slug' => 'ninh-binh-phong-nha', 'title' => 'Vé xe Ninh Bình - Phong Nha', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Ninh Bình - Phong Nha', 'duration' => '7h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/phong-nha.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/phong-nha.jpg"]', 'content' => null, 'priority' => 23, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 24, 'province_start_id' => 8, 'province_end_id' => 7, 'name' => 'Phong Nha - Ninh Bình', 'slug' => 'phong-nha-ninh-binh', 'title' => 'Vé xe Phong Nha - Ninh Bình', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Phong Nha - Ninh Bình', 'duration' => '7h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 24, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 25, 'province_start_id' => 7, 'province_end_id' => 9, 'name' => 'Ninh Bình - Huế', 'slug' => 'ninh-binh-hue', 'title' => 'Vé xe Ninh Bình - Huế', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Ninh Bình - Huế', 'duration' => '10h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hue.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hue.jpg"]', 'content' => null, 'priority' => 25, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 26, 'province_start_id' => 9, 'province_end_id' => 7, 'name' => 'Huế - Ninh Bình', 'slug' => 'hue-ninh-binh', 'title' => 'Vé xe Huế - Ninh Bình', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Huế - Ninh Bình', 'duration' => '10h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 26, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 27, 'province_start_id' => 7, 'province_end_id' => 10, 'name' => 'Ninh Bình - Đà Nẵng', 'slug' => 'ninh-binh-da-nang', 'title' => 'Vé xe Ninh Bình - Đà Nẵng', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Ninh Bình - Đà Nẵng', 'duration' => '13h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/da-nang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/da-nang.jpg"]', 'content' => null, 'priority' => 27, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 28, 'province_start_id' => 10, 'province_end_id' => 7, 'name' => 'Đà Nẵng - Ninh Bình', 'slug' => 'da-nang-ninh-binh', 'title' => 'Vé xe Đà Nẵng - Ninh Bình', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Đà Nẵng - Ninh Bình', 'duration' => '13h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 28, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 29, 'province_start_id' => 7, 'province_end_id' => 11, 'name' => 'Ninh Bình - Hội An', 'slug' => 'ninh-binh-hoi-an', 'title' => 'Vé xe Ninh Bình - Hội An', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Ninh Bình - Hội An', 'duration' => '14h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hoi-an.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hoi-an.jpg"]', 'content' => null, 'priority' => 29, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 30, 'province_start_id' => 11, 'province_end_id' => 7, 'name' => 'Hội An - Ninh Bình', 'slug' => 'hoi-an-ninh-binh', 'title' => 'Vé xe Hội An - Ninh Bình', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hội An - Ninh Bình', 'duration' => '14h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 30, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 31, 'province_start_id' => 10, 'province_end_id' => 11, 'name' => 'Đà Nẵng - Hội An', 'slug' => 'da-nang-hoi-an', 'title' => 'Vé xe Đà Nẵng - Hội An', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Đà Nẵng - Hội An', 'duration' => '1h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hoi-an.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hoi-an.jpg"]', 'content' => null, 'priority' => 31, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 32, 'province_start_id' => 11, 'province_end_id' => 10, 'name' => 'Hội An - Đà Nẵng', 'slug' => 'hoi-an-da-nang', 'title' => 'Vé xe Hội An - Đà Nẵng', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hội An - Đà Nẵng', 'duration' => '1h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/da-nang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/da-nang.jpg"]', 'content' => null, 'priority' => 32, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 33, 'province_start_id' => 9, 'province_end_id' => 11, 'name' => 'Huế - Hội An', 'slug' => 'hue-hoi-an', 'title' => 'Vé xe Huế - Hội An', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Huế - Hội An', 'duration' => '4h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hoi-an.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hoi-an.jpg"]', 'content' => null, 'priority' => 33, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 34, 'province_start_id' => 11, 'province_end_id' => 9, 'name' => 'Hội An - Huế', 'slug' => 'hoi-an-hue', 'title' => 'Vé xe Hội An - Huế', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hội An - Huế', 'duration' => '4h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hue.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hue.jpg"]', 'content' => null, 'priority' => 34, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 35, 'province_start_id' => 9, 'province_end_id' => 10, 'name' => 'Huế - Đà Nẵng', 'slug' => 'hue-da-nang', 'title' => 'Vé xe Huế - Đà Nẵng', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Huế - Đà Nẵng', 'duration' => '3h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/da-nang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/da-nang.jpg"]', 'content' => null, 'priority' => 35, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 36, 'province_start_id' => 10, 'province_end_id' => 9, 'name' => 'Đà Nẵng - Huế', 'slug' => 'da-nang-hue', 'title' => 'Vé xe Đà Nẵng - Huế', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Đà Nẵng - Huế', 'duration' => '3h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hue.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hue.jpg"]', 'content' => null, 'priority' => 36, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 37, 'province_start_id' => 8, 'province_end_id' => 11, 'name' => 'Phong Nha - Hội An', 'slug' => 'phong-nha-hoi-an', 'title' => 'Vé xe Phong Nha - Hội An', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Phong Nha - Hội An', 'duration' => '7h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hoi-an.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hoi-an.jpg"]', 'content' => null, 'priority' => 37, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 38, 'province_start_id' => 11, 'province_end_id' => 8, 'name' => 'Hội An - Phong Nha', 'slug' => 'hoi-an-phong-nha', 'title' => 'Vé xe Hội An - Phong Nha', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hội An - Phong Nha', 'duration' => '7h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/phong-nha.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/phong-nha.jpg"]', 'content' => null, 'priority' => 38, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 39, 'province_start_id' => 8, 'province_end_id' => 10, 'name' => 'Phong Nha - Đà Nẵng', 'slug' => 'phong-nha-da-nang', 'title' => 'Vé xe Phong Nha - Đà Nẵng', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Phong Nha - Đà Nẵng', 'duration' => '6h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/da-nang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/da-nang.jpg"]', 'content' => null, 'priority' => 39, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 40, 'province_start_id' => 10, 'province_end_id' => 8, 'name' => 'Đà Nẵng - Phong Nha', 'slug' => 'da-nang-phong-nha', 'title' => 'Vé xe Đà Nẵng - Phong Nha', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Đà Nẵng - Phong Nha', 'duration' => '6h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/phong-nha.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/phong-nha.jpg"]', 'content' => null, 'priority' => 40, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 41, 'province_start_id' => 8, 'province_end_id' => 9, 'name' => 'Phong Nha - Huế', 'slug' => 'phong-nha-hue', 'title' => 'Vé xe Phong Nha - Huế', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Phong Nha - Huế', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/hue.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/hue.jpg"]', 'content' => null, 'priority' => 41, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 42, 'province_start_id' => 9, 'province_end_id' => 8, 'name' => 'Huế - Phong Nha', 'slug' => 'hue-phong-nha', 'title' => 'Vé xe Huế - Phong Nha', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Huế - Phong Nha', 'duration' => '3,5h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/phong-nha.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/phong-nha.jpg"]', 'content' => null, 'priority' => 42, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 43, 'province_start_id' => 6, 'province_end_id' => 7, 'name' => 'Hà Giang - Ninh Bình', 'slug' => 'ha-giang-ninh-binh', 'title' => 'Vé xe Hà Giang - Ninh Bình', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Giang - Ninh Bình', 'duration' => '8h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ninh-binh.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ninh-binh.jpg"]', 'content' => null, 'priority' => 43, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 44, 'province_start_id' => 7, 'province_end_id' => 6, 'name' => 'Ninh Bình - Hà Giang', 'slug' => 'ninh-binh-ha-giang', 'title' => 'Vé xe Ninh Bình - Hà Giang', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Ninh Bình - Hà Giang', 'duration' => '8h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-giang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-giang.jpg"]', 'content' => null, 'priority' => 44, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 45, 'province_start_id' => 6, 'province_end_id' => 5, 'name' => 'Hà Giang - Cát Bà', 'slug' => 'ha-giang-cat-ba', 'title' => 'Vé xe Hà Giang - Cát Bà', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Giang - Cát Bà', 'duration' => '12h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/cat-ba.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/cat-ba.jpg"]', 'content' => null, 'priority' => 45, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 46, 'province_start_id' => 5, 'province_end_id' => 6, 'name' => 'Cát Bà - Hà Giang', 'slug' => 'cat-ba-ha-giang', 'title' => 'Vé xe Cát Bà - Hà Giang', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Cát Bà - Hà Giang', 'duration' => '12h', 'distance_km' => null, 'thumbnail_url' => '/userfiles/files/city_imgs/ha-giang.jpg', 'image_list_url' => '["/userfiles/files/city_imgs/ha-giang.jpg"]', 'content' => null, 'priority' => 46, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
        ]);
    }

    private function seedCompanyRoutes(): void
    {
        DB::table('company_routes')->insert([
            ['id' => 1, 'company_id' => 1, 'route_id' => 1, 'name' => 'Hà Nội - Sapa - King Express Bus', 'slug' => 'ha-noi-sapa-king-express-bus', 'title' => 'Vé xe Hà Nội - Sapa', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Hà Nội - Sapa', 'duration' => '6h', 'available_hotel_pickup' => 1, 'priority' => 1, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'company_id' => 1, 'route_id' => 2, 'name' => 'Sapa - Hà Nội - King Express Bus', 'slug' => 'sapa-ha-noi-king-express-bus', 'title' => 'Vé xe Sapa - Hà Nội', 'description' => 'Đặt vé xe giường nằm, cabin, limousine chất lượng cao tuyến Sapa - Hà Nội', 'duration' => '6h', 'available_hotel_pickup' => 0, 'priority' => 2, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            // ... and so on for all 46 routes
        ]);
    }

    private function seedCompanyRouteStops(): void
    {
        DB::table('company_route_stops')->insert([
            ['id' => 1, 'company_route_id' => 1, 'stop_id' => 1, 'stop_type' => 'pickup', 'priority' => 1],
            ['id' => 2, 'company_route_id' => 1, 'stop_id' => 2, 'stop_type' => 'dropoff', 'priority' => 2],
            ['id' => 3, 'company_route_id' => 2, 'stop_id' => 2, 'stop_type' => 'pickup', 'priority' => 1],
            ['id' => 4, 'company_route_id' => 2, 'stop_id' => 1, 'stop_type' => 'dropoff', 'priority' => 2],
            // ... and so on for all 92 stops
        ]);
    }

    private function seedBusRoutes(): void
    {
        DB::table('bus_routes')->insert([
            ['id' => 1, 'bus_id' => 1, 'company_route_id' => 1, 'start_time' => '07:00:00', 'end_time' => '13:00:00', 'price' => 270000, 'is_active' => 1, 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'bus_id' => 1, 'company_route_id' => 1, 'start_time' => '22:00:00', 'end_time' => '04:00:00', 'price' => 270000, 'is_active' => 1, 'priority' => 0, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            // ... and so on for all 227 bus routes
        ]);
    }

    private function seedMenus(): void
    {
        DB::table('menus')->insert([
            ['id' => 1, 'name' => 'Trang chủ', 'url' => '/', 'parent_id' => null, 'priority' => 0, 'type' => 'custom_link', 'related_id' => null, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'name' => 'Tuyến đường', 'url' => '#', 'parent_id' => null, 'priority' => 1, 'type' => 'custom_link', 'related_id' => null, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'name' => 'Liên Hệ', 'url' => '/lien-he', 'parent_id' => null, 'priority' => 2, 'type' => 'custom_link', 'related_id' => null, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'name' => 'Hà Nội - Sapa', 'url' => '/tuyen-duong/ha-noi-sapa', 'parent_id' => 2, 'priority' => 0, 'type' => 'route', 'related_id' => 1, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 5, 'name' => 'Sapa - Hà Nội', 'url' => '/tuyen-duong/sapa-ha-noi', 'parent_id' => 2, 'priority' => 1, 'type' => 'route', 'related_id' => 2, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 6, 'name' => 'Hà Nội - Huế', 'url' => '/tuyen-duong/ha-noi-hue', 'parent_id' => 2, 'priority' => 2, 'type' => 'route', 'related_id' => 3, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 7, 'name' => 'Hà Nội - Đà Nẵng', 'url' => '/tuyen-duong/ha-noi-da-nang', 'parent_id' => 2, 'priority' => 3, 'type' => 'route', 'related_id' => 4, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 8, 'name' => 'Hà Nội - Hà Giang', 'url' => '/tuyen-duong/ha-noi-ha-giang', 'parent_id' => 2, 'priority' => 4, 'type' => 'route', 'related_id' => 5, 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
        ]);
    }

    private function seedBookings(): void
    {
        DB::table('bookings')->insert([
            ['id' => 1, 'booking_code' => 'KEBXWZNF8FP', 'user_id' => 2, 'bus_route_id' => 1, 'booking_date' => '2025-10-20', 'customer_name' => 'Nguyễn Văn An', 'customer_email' => 'nguyenvanan@gmail.com', 'customer_phone' => '0123456789', 'pickup_stop_id' => 1, 'dropoff_stop_id' => 2, 'quantity' => 2, 'total_price' => 540000, 'status' => 'confirmed', 'payment_method' => 'online_banking', 'payment_status' => 'paid', 'notes' => 'Ghi chú cho đơn hàng 1', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 2, 'booking_code' => 'KEBAVNBMMJT', 'user_id' => 3, 'bus_route_id' => 10, 'booking_date' => '2025-11-15', 'customer_name' => 'Trần Thị Bình', 'customer_email' => 'tranthibinh@gmail.com', 'customer_phone' => '0987123456', 'pickup_stop_id' => 2, 'dropoff_stop_id' => 1, 'quantity' => 1, 'total_price' => 400000, 'status' => 'pending', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid', 'notes' => 'Ghi chú cho đơn hàng 2', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 3, 'booking_code' => 'KEBJDQELCXB', 'user_id' => 5, 'bus_route_id' => 3, 'booking_date' => '2025-09-25', 'customer_name' => 'Minh Long', 'customer_email' => 'deocomate@gmail.com', 'customer_phone' => '0565651189', 'pickup_stop_id' => 1, 'dropoff_stop_id' => 2, 'quantity' => 1, 'total_price' => 400000, 'status' => 'confirmed', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid', 'notes' => 'Ghi chú cho đơn hàng 3', 'created_at' => '2025-10-03 06:27:41', 'updated_at' => '2025-10-03 06:27:41'],
            ['id' => 4, 'booking_code' => 'FHCDAPKA', 'user_id' => null, 'bus_route_id' => 1, 'booking_date' => '2025-10-03', 'customer_name' => 'Minh Long', 'customer_email' => 'deocomate@gmail.com', 'customer_phone' => '0999888777', 'pickup_stop_id' => null, 'dropoff_stop_id' => 2, 'quantity' => 1, 'total_price' => 270000, 'status' => 'confirmed', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid', 'notes' => '[Đón tại khách sạn]: Khach san???\n[Ghi chú của khách]: 19 Hang Thiec Hoan Kiem Ha Noi', 'created_at' => '2025-10-03 06:29:29', 'updated_at' => '2025-10-03 06:29:29'],
            ['id' => 5, 'booking_code' => '9N7OBWDL', 'user_id' => null, 'bus_route_id' => 86, 'booking_date' => '2026-01-26', 'customer_name' => 'Hayden Smith', 'customer_email' => 'mail@chambalam.com', 'customer_phone' => '+61415173146', 'pickup_stop_id' => 6, 'dropoff_stop_id' => 1, 'quantity' => 2, 'total_price' => 840000, 'status' => 'confirmed', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid', 'notes' => null, 'created_at' => '2025-10-04 10:42:56', 'updated_at' => '2025-10-04 10:42:56'],
            ['id' => 6, 'booking_code' => 'BOGCJ2VS', 'user_id' => null, 'bus_route_id' => 66, 'booking_date' => '2025-10-04', 'customer_name' => 'Hayden Smith', 'customer_email' => 'mail@chambalam.com', 'customer_phone' => '+61415173146', 'pickup_stop_id' => 1, 'dropoff_stop_id' => 6, 'quantity' => 2, 'total_price' => 740000, 'status' => 'confirmed', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid', 'notes' => null, 'created_at' => '2025-10-04 11:05:18', 'updated_at' => '2025-10-04 11:05:18'],
            ['id' => 7, 'booking_code' => 'MTF2VJ7X', 'user_id' => null, 'bus_route_id' => 77, 'booking_date' => '2026-01-27', 'customer_name' => 'Hayden', 'customer_email' => 'mail@chambalam.com', 'customer_phone' => '+61415173146', 'pickup_stop_id' => 6, 'dropoff_stop_id' => 1, 'quantity' => 2, 'total_price' => 740000, 'status' => 'confirmed', 'payment_method' => 'cash_on_pickup', 'payment_status' => 'unpaid', 'notes' => null, 'created_at' => '2025-10-06 04:28:50', 'updated_at' => '2025-10-06 04:28:50'],
            ['id' => 8, 'booking_code' => 'HKG0FZTQ', 'user_id' => null, 'bus_route_id' => 1, 'booking_date' => '2025-10-06', 'customer_name' => 'Minh Loing', 'customer_email' => 'deocomate@gmail.com', 'customer_phone' => '0865095066', 'pickup_stop_id' => null, 'dropoff_stop_id' => 2, 'quantity' => 1, 'total_price' => 270000, 'status' => 'pending', 'payment_method' => 'online_banking', 'payment_status' => 'unpaid', 'notes' => '[Đón tại khách sạn]: 19 Hang Thiec\n[Ghi chú của khách]: ABC', 'created_at' => '2025-10-06 08:38:38', 'updated_at' => '2025-10-06 08:38:38'],
        ]);
    }
}
