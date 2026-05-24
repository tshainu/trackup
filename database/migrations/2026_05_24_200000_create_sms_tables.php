<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('sender_id')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('sms_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('message');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default settings row
        DB::table('sms_settings')->insert([
            'api_url'   => '',
            'api_key'   => '',
            'sender_id' => '',
            'enabled'   => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed default templates
        $templates = [
            [
                'key'     => 'job_created',
                'label'   => 'New Job Order Created',
                'message' => 'Dear {customer_name}, your job order {order_no} has been received. We will contact you shortly. - {store_name}',
            ],
            [
                'key'     => 'job_status_changed',
                'label'   => 'Job Order Status Updated',
                'message' => 'Dear {customer_name}, your order {order_no} status has been updated to: {status}. For queries call us. - {store_name}',
            ],
            [
                'key'     => 'field_complaint_created',
                'label'   => 'Field Service Request Logged',
                'message' => 'Dear {customer_name}, your service request {complaint_no} has been logged. Our team will reach you soon. - {store_name}',
            ],
            [
                'key'     => 'field_service_completed',
                'label'   => 'Field Service Completed',
                'message' => 'Dear {customer_name}, field service {complaint_no} has been completed by {technician}. Thank you! - {store_name}',
            ],
        ];

        foreach ($templates as $t) {
            DB::table('sms_templates')->insert(array_merge($t, [
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
        Schema::dropIfExists('sms_settings');
    }
};
