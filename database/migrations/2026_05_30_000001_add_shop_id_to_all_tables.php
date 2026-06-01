<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add shop_id to job_cards
        if (!Schema::hasColumn('job_cards', 'shop_id')) {
            Schema::table('job_cards', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Add shop_id to employees
        if (!Schema::hasColumn('employees', 'shop_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Add shop_id to customers
        if (!Schema::hasColumn('customers', 'shop_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Add shop_id to store_info
        if (!Schema::hasColumn('store_info', 'shop_id')) {
            Schema::table('store_info', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Add shop_id to service_types
        if (!Schema::hasColumn('service_types', 'shop_id')) {
            Schema::table('service_types', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Add shop_id to sms_settings
        if (!Schema::hasColumn('sms_settings', 'shop_id')) {
            Schema::table('sms_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Add shop_id to field_complaints
        if (!Schema::hasColumn('field_complaints', 'shop_id')) {
            Schema::table('field_complaints', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
                $table->index('shop_id');
            });
        }

        // Assign all existing data to shop_id = 1 (SOS Shop)
        DB::statement('UPDATE job_cards SET shop_id = 1 WHERE shop_id IS NULL');
        DB::statement('UPDATE employees SET shop_id = 1 WHERE shop_id IS NULL');
        DB::statement('UPDATE customers SET shop_id = 1 WHERE shop_id IS NULL');
        DB::statement('UPDATE store_info SET shop_id = 1 WHERE shop_id IS NULL');
        DB::statement('UPDATE service_types SET shop_id = 1 WHERE shop_id IS NULL');
        DB::statement('UPDATE sms_settings SET shop_id = 1 WHERE shop_id IS NULL');
        DB::statement('UPDATE field_complaints SET shop_id = 1 WHERE shop_id IS NULL');
    }

    public function down(): void
    {
        foreach (['job_cards','employees','customers','store_info','service_types','sms_settings','field_complaints'] as $table) {
            if (Schema::hasColumn($table, 'shop_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropColumn('shop_id');
                });
            }
        }
    }
};
