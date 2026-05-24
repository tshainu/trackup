<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->decimal('advance_amount', 10, 2)->default(0)->after('rupees');
        });
    }

    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn('advance_amount');
        });
    }
};
