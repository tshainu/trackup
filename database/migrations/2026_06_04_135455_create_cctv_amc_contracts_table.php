<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_amc_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('amc_no', 30)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('contract_value', 10, 2)->default(0);
            $table->enum('visit_frequency', ['Monthly', 'Quarterly', 'Half Yearly', 'Yearly'])->default('Quarterly');
            $table->integer('visits_included')->default(4);
            $table->integer('visits_used')->default(0);
            $table->enum('status', ['Active', 'Expired', 'Cancelled', 'Pending'])->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cctv_amc_visits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->unsignedBigInteger('amc_id');
            $table->date('visit_date');
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['Scheduled', 'Completed', 'Missed'])->default('Scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_amc_visits');
        Schema::dropIfExists('cctv_amc_contracts');
    }
};
