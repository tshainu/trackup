<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('customer_id')->unique();       // CUS-001
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('nic')->nullable();
            $table->text('address')->nullable();
            // GPS
            $table->decimal('gps_lat', 10, 7)->nullable();
            $table->decimal('gps_lng', 10, 7)->nullable();
            $table->string('gps_label')->nullable();       // e.g. "Home", "Shop"
            $table->text('gps_raw_link')->nullable();      // original link pasted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
