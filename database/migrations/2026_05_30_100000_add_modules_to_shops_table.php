<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->json('modules')->nullable()->after('notes');
        });

        // Default all existing shops to have all modules
        DB::table('shops')->whereNull('modules')->update([
            'modules' => json_encode(['job_orders', 'field_services']),
        ]);
    }

    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('modules');
        });
    }
};
