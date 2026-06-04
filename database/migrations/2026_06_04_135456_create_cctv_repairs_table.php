<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_repairs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('repair_no', 30)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable();
            $table->string('device_type', 100);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->text('fault_description');
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->text('repair_notes')->nullable();
            $table->json('parts_used')->nullable(); // [{name, qty, cost}]
            $table->decimal('repair_cost', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('received_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->enum('status', ['Received', 'Diagnosing', 'Repairing', 'Testing', 'Ready', 'Collected', 'Cancelled'])->default('Received');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cctv_repairs'); }
};
