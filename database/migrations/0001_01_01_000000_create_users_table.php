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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 1000);
            $table->string('email')->unique()->nullable(); // Giảm độ dài về mặc định (255)
            $table->string('phone', 1000)->nullable();
            $table->string('address', 1000)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 1000)->nullable();
            $table->enum('role', ['admin', 'company', 'customer', 'guest'])->default('customer');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Giảm độ dài về mặc định (255)
            $table->string('token', 1000);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Giảm độ dài về mặc định (255)
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
