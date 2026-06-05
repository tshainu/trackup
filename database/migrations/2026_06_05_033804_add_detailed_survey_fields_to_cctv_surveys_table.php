<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cctv_surveys', function (Blueprint $table) {
            // Survey meta
            $table->enum('survey_type', ['New Site', 'Upgrading', 'Modification', 'Service'])->default('New Site')->after('status');
            $table->enum('survey_mode', ['Detailed', 'Simple'])->default('Detailed')->after('survey_type');

            // Section 1 – Customer
            $table->string('contact_person', 100)->nullable()->after('mobile');
            $table->string('alt_mobile', 20)->nullable()->after('contact_person');
            $table->string('email', 150)->nullable()->after('alt_mobile');
            $table->string('gps_location', 255)->nullable()->after('email');
            $table->enum('customer_type', ['House', 'Shop', 'Office', 'Factory', 'School', 'Hotel', 'Government', 'Other'])->nullable()->after('gps_location');
            $table->string('customer_type_other', 100)->nullable()->after('customer_type');

            // Section 2 – Site
            $table->string('building_name', 150)->nullable()->after('customer_type_other');
            $table->string('building_type', 100)->nullable()->after('building_name');
            $table->string('site_size', 100)->nullable()->after('building_type');
            $table->boolean('existing_security_system')->default(false)->after('site_size');
            $table->enum('construction_status', ['Existing', 'Under Construction', 'New Building'])->nullable()->after('existing_security_system');

            // Section 3 – Requirements/Purposes
            $table->json('purposes')->nullable()->after('construction_status');

            // Section 4 – Camera Locations
            $table->json('camera_locations')->nullable()->after('purposes');

            // Section 5 – Network
            $table->enum('internet_status', ['Available', 'Not Available'])->nullable()->after('camera_locations');
            $table->enum('isp', ['SLT', 'Dialog', 'Starlink', 'Other'])->nullable()->after('internet_status');
            $table->string('isp_other', 100)->nullable()->after('isp');
            $table->boolean('wifi_coverage')->default(false)->after('isp_other');
            $table->boolean('lan_available')->default(false)->after('wifi_coverage');

            // Section 6 – Power
            $table->enum('power_availability', ['Stable', 'Moderate', 'Poor'])->nullable()->after('lan_available');
            $table->boolean('ups_required')->default(false)->after('power_availability');
            $table->boolean('electrical_work_required')->default(false)->after('ups_required');
            $table->boolean('voltage_issues')->default(false)->after('electrical_work_required');

            // Section 7 – Installation
            $table->enum('cable_route', ['Easy', 'Medium', 'Difficult'])->nullable()->after('voltage_issues');
            $table->enum('ceiling_type', ['Concrete', 'Gypsum', 'Metal', 'Wooden'])->nullable()->after('cable_route');
            $table->enum('wall_type', ['Brick', 'Concrete', 'Partition'])->nullable()->after('ceiling_type');
            $table->boolean('ladder_required')->default(false)->after('wall_type');
            $table->boolean('scaffolding_required')->default(false)->after('ladder_required');
            $table->tinyInteger('height_risk')->default(0)->after('scaffolding_required'); // 0–10
            $table->string('special_safety_equipment', 255)->nullable()->after('height_risk');

            // Section 8 – Material Estimation
            $table->integer('cameras_qty')->default(0)->after('special_safety_equipment');
            $table->integer('dvr_channels')->default(0)->after('cameras_qty');
            $table->integer('hdd_storage_days')->default(30)->after('dvr_channels');
            $table->integer('cable_meters')->default(0)->after('hdd_storage_days');
            $table->json('accessories')->nullable()->after('cable_meters');

            // Section 10 – Risks
            $table->json('risks')->nullable()->after('site_photos');
        });
    }

    public function down(): void
    {
        Schema::table('cctv_surveys', function (Blueprint $table) {
            $table->dropColumn([
                'survey_type', 'survey_mode',
                'contact_person', 'alt_mobile', 'email', 'gps_location', 'customer_type', 'customer_type_other',
                'building_name', 'building_type', 'site_size', 'existing_security_system', 'construction_status',
                'purposes', 'camera_locations',
                'internet_status', 'isp', 'isp_other', 'wifi_coverage', 'lan_available',
                'power_availability', 'ups_required', 'electrical_work_required', 'voltage_issues',
                'cable_route', 'ceiling_type', 'wall_type', 'ladder_required', 'scaffolding_required',
                'height_risk', 'special_safety_equipment',
                'cameras_qty', 'dvr_channels', 'hdd_storage_days', 'cable_meters', 'accessories',
                'risks',
            ]);
        });
    }
};
