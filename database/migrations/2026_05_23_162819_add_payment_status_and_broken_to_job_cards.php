<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            // 'partial' = partially paid, still in active list
            // 'paid'    = fully paid (used when delivered without archiving, edge case)
            $table->string('payment_status')->nullable()->after('paid_amount'); // null | partial | paid
        });
    }

    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
