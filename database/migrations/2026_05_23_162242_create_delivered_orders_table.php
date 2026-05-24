<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivered_orders', function (Blueprint $table) {
            $table->id();

            // Job card identifiers
            $table->string('order_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('customer_id')->nullable();

            // Customer info
            $table->string('customer_name');
            $table->string('customer_address')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_nic')->nullable();
            $table->string('customer_dob')->nullable();
            $table->string('phone_no')->nullable();

            // Device info
            $table->string('device_name')->nullable();
            $table->string('device_brand')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('device_age')->nullable();
            $table->string('device_fault')->nullable();
            $table->text('issue')->nullable();

            // Job info
            $table->date('date')->nullable();
            $table->decimal('rupees', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->string('status')->default('Completed');
            $table->string('priority')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->text('accessories')->nullable();
            $table->text('remark')->nullable();
            $table->boolean('need_assistant')->default(false);
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->boolean('payment_received')->default(true);

            // Invoice line items snapshot (JSON)
            $table->json('invoice_items')->nullable();

            // Delivery record
            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivered_orders');
    }
};
