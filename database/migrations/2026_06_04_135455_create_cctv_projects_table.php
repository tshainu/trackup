<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('project_no', 30)->unique();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->date('installation_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->json('team_assigned')->nullable(); // [employee_ids]
            $table->string('signature_path')->nullable();
            $table->enum('stage', ['Survey Complete', 'Materials Ready', 'Installation Started', 'Configuration', 'Testing', 'Customer Handover', 'Warranty Activated'])->default('Survey Complete');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cctv_projects'); }
};
