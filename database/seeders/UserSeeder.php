<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
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
                'id' => 4,
                'name' => 'Lê Hoàng Cường',
                'email' => 'lehoangcuong@gmail.com',
                'phone' => '0369852147',
                'address' => '789 Đường Nguyễn Văn Linh, Đà Nẵng',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'name' => 'Nhà xe KingExpress',
                'email' => 'company@kingexpressbus.com',
                'phone' => '0865095066',
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
}
