<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cctv_projects', function (Blueprint $table) {
            $table->string('status', 30)->default('scheduled')->after('stage');
            $table->date('start_date')->nullable()->after('status');
            $table->date('end_date')->nullable()->after('start_date');
            $table->string('technician_name')->nullable()->after('end_date'); // comma-joined names
            $table->json('technician_ids')->nullable()->after('technician_name');
            $table->unsignedSmallInteger('camera_count')->default(0)->after('technician_ids');
            $table->decimal('contract_amount', 12, 2)->default(0)->after('camera_count');
            $table->decimal('advance_paid', 12, 2)->default(0)->after('contract_amount');
            $table->text('scope')->nullable()->after('advance_paid');
            $table->json('equipment_list')->nullable()->after('scope');
        });
    }

    public function down(): void
    {
        Schema::table('cctv_projects', function (Blueprint $table) {
            $table->dropColumn(['status','start_date','end_date','technician_name','technician_ids',
                'camera_count','contract_amount','advance_paid','scope','equipment_list']);
        });
    }
};
