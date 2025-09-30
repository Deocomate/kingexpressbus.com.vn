<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bus_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('icon', 100)->nullable()->comment('Font Awesome icon class, e.g., fas fa-wifi');
            $table->integer('priority')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_services');
    }
};
