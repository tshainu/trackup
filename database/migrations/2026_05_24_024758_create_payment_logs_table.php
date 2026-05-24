<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_card_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('note')->nullable();          // e.g. "Advance", "Final payment"
            $table->timestamp('paid_at')->useCurrent();  // date/time of payment
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
