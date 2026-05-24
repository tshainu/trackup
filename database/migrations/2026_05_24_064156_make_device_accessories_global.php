<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Wipe existing rows
        DB::table('device_accessories')->truncate();

        Schema::table('device_accessories', function (Blueprint $table) {
            $table->dropForeign(['device_list_id']);
        });

        Schema::table('device_accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('device_list_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('device_accessories', function (Blueprint $table) {
            $table->unsignedBigInteger('device_list_id')->nullable(false)->change();
        });
    }
};
