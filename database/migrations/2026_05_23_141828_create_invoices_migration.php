<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add invoice fields to job_cards
        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('invoice_no')->nullable()->after('order_no');
            $table->date('invoice_date')->nullable()->after('invoice_no');
            $table->decimal('discount', 10, 2)->default(0)->after('rupees');
            $table->decimal('paid_amount', 10, 2)->default(0)->after('discount');
        });

        // Invoice line items (spare parts / labour etc.)
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained('job_cards')->cascadeOnDelete();
            $table->string('description');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->integer('qty')->default(1);
            $table->decimal('total', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'invoice_date', 'discount', 'paid_amount']);
        });
    }
};
