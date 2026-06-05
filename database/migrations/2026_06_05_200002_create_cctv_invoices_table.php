<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('invoice_no', 30)->unique();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('quotation_id')->nullable();
            $table->unsignedBigInteger('lead_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->json('equipment_list')->nullable();
            $table->decimal('labour_cost', 12, 2)->default(0);
            $table->decimal('installation_cost', 12, 2)->default(0);
            $table->decimal('transport_cost', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->string('status', 20)->default('Unpaid'); // Unpaid, Partial, Paid
            $table->date('invoice_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_invoices');
    }
};
