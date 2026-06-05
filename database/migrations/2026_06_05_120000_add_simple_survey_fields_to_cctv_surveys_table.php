<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cctv_surveys', function (Blueprint $table) {
            // Simple survey fields
            $table->integer('simple_num_cameras')->default(0)->after('risks');
            $table->enum('simple_dvr_nvr', ['DVR', 'NVR'])->nullable()->after('simple_num_cameras');
            $table->integer('simple_dvr_channels')->nullable()->after('simple_dvr_nvr');
            $table->boolean('simple_internet_available')->default(false)->after('simple_dvr_channels');
            $table->enum('simple_isp', ['SLT', 'Dialog', 'Starlink', 'Other', 'None'])->nullable()->after('simple_internet_available');
            $table->tinyInteger('simple_cabling_ease')->default(5)->after('simple_isp');   // 1–10
            $table->tinyInteger('simple_risk_level')->default(5)->after('simple_cabling_ease'); // 1–10
            $table->integer('simple_num_technicians')->default(1)->after('simple_risk_level');
            $table->integer('simple_estimated_days')->default(1)->after('simple_num_technicians');
            $table->string('simple_gps_location', 255)->nullable()->after('simple_estimated_days');
            $table->text('simple_remark')->nullable()->after('simple_gps_location');
        });
    }

    public function down(): void
    {
        Schema::table('cctv_surveys', function (Blueprint $table) {
            $table->dropColumn([
                'simple_num_cameras', 'simple_dvr_nvr', 'simple_dvr_channels',
                'simple_internet_available', 'simple_isp',
                'simple_cabling_ease', 'simple_risk_level',
                'simple_num_technicians', 'simple_estimated_days',
                'simple_gps_location', 'simple_remark',
            ]);
        });
    }
};
