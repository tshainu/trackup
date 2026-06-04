<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('survey_no', 30)->unique();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable();
            $table->date('survey_date')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->json('site_photos')->nullable();
            $table->integer('num_floors')->default(1);
            $table->integer('indoor_cameras')->default(0);
            $table->integer('outdoor_cameras')->default(0);
            $table->boolean('internet_available')->default(false);
            $table->boolean('existing_cctv')->default(false);
            $table->text('special_notes')->nullable();
            $table->enum('status', ['Scheduled', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cctv_surveys'); }
};
