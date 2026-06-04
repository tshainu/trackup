<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // reference_no + device_photo on job_cards
        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('reference_no')->nullable()->after('order_no');
            $table->string('device_photo')->nullable()->after('device_fault');
        });

        // reference_no on field_complaints
        Schema::table('field_complaints', function (Blueprint $table) {
            $table->string('reference_no')->nullable()->after('complaint_no');
        });

        // milestones JSON template on service_types
        Schema::table('service_types', function (Blueprint $table) {
            $table->json('milestones')->nullable()->after('description');
        });

        // Add shop_id to whatsapp_settings
        if (!Schema::hasColumn('whatsapp_settings', 'shop_id')) {
            Schema::table('whatsapp_settings', function (Blueprint $table) {
                $table->unsignedBigInteger('shop_id')->nullable()->after('id');
            });
        }

        // quotation + uncollected reminder templates
        $templates = [
            [
                'key'     => 'quotation',
                'label'   => 'Quotation / Estimate',
                'message' => "Dear {customer_name}, here is your repair estimate for {device}.\nEstimate: Rs. {total}\nOrder: {order_no} — {store_name}",
            ],
            [
                'key'     => 'uncollected_reminder',
                'label'   => 'Uncollected Item Reminder',
                'message' => "Dear {customer_name}, your repaired device ({device}) is ready for collection.\nOrder: {order_no} | Days waiting: {days_waiting}\nPlease visit us at {store_name} at your earliest convenience.",
            ],
        ];

        foreach ($templates as $t) {
            $exists = DB::table('whatsapp_templates')->where('key', $t['key'])->exists();
            if (!$exists) {
                DB::table('whatsapp_templates')->insert(array_merge($t, [
                    'active'     => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        // uncollected_reminder_settings column on whatsapp_settings
        if (!Schema::hasColumn('whatsapp_settings', 'uncollected_reminder_count')) {
            Schema::table('whatsapp_settings', function (Blueprint $table) {
                $table->unsignedTinyInteger('uncollected_reminder_count')->default(3)->after('enabled');
                $table->unsignedInteger('uncollected_reminder_interval_hours')->default(48)->after('uncollected_reminder_count');
                $table->boolean('uncollected_reminder_enabled')->default(false)->after('uncollected_reminder_interval_hours');
            });
        }
    }

    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn(['reference_no', 'device_photo']);
        });
        Schema::table('field_complaints', function (Blueprint $table) {
            $table->dropColumn('reference_no');
        });
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('milestones');
        });
    }
};
