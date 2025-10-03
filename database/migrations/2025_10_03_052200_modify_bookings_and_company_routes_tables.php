<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Cho phép cột pickup_stop_id có thể nhận giá trị NULL
            $table->unsignedBigInteger('pickup_stop_id')->nullable()->change();
        });

        Schema::table('company_routes', function (Blueprint $table) {
            // Thêm cột cờ để bật/tắt tính năng đón tại khách sạn cho các chuyến ở nhà xe
            $table->boolean('available_hotel_pickup')->default(false)->comment("Cờ bật/tắt tính năng đón tại khách sạn cho các chuyến của nhà xe");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Hoàn tác lại, bắt buộc cột phải có giá trị
            $table->unsignedBigInteger('pickup_stop_id')->nullable(false)->change();
        });

        Schema::table('company_routes', function (Blueprint $table) {
            // Xóa cột đã thêm
            $table->dropColumn('available_hotel_pickup');
        });
    }
};
