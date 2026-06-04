<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('lead_no', 30)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20);
            $table->text('address')->nullable();
            $table->enum('customer_type', ['Residential', 'Commercial', 'Government'])->default('Residential');
            $table->date('inquiry_date')->nullable();
            $table->string('inquiry_source', 100)->nullable();
            $table->text('requirement_notes')->nullable();
            $table->enum('status', ['New Lead', 'Survey Scheduled', 'Survey Completed', 'Quotation Sent', 'Approved', 'Lost'])->default('New Lead');
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cctv_leads'); }
};
