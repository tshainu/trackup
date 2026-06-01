<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Super Admins
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });

        // Shops
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('shop_name');
            $table->string('shop_code')->unique();
            $table->string('owner_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->default('Sri Lanka');
            $table->string('logo')->nullable();
            $table->string('admin_username');
            $table->string('admin_password_hash');
            $table->string('admin_plain_password'); // stored for super admin reference
            $table->enum('status', ['active', 'suspended', 'pending'])->default('active');
            $table->timestamp('last_active_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Shop activity log
        Schema::create('shop_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')->constrained('shops')->onDelete('cascade');
            $table->string('action');
            $table->text('description')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('super_admins')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_activity_logs');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('super_admins');
    }
};
