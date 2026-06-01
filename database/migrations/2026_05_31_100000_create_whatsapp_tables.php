<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('api_url')->nullable();
            $table->string('api_key')->nullable();
            $table->string('instance_id')->nullable();
            $table->string('phone_number_id')->nullable();
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('whatsapp_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('label');
            $table->text('message');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        // Seed default settings row
        DB::table('whatsapp_settings')->insert([
            'api_url'         => '',
            'api_key'         => '',
            'instance_id'     => '',
            'phone_number_id' => '',
            'enabled'         => 0,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // Seed default templates
        $templates = [
            [
                'key'     => 'invoice_sent',
                'label'   => 'Invoice Sent to Customer',
                'message' => "Dear {customer_name}, your invoice #{invoice_no} for {device} is ready.\nTotal: Rs. {total} | Balance: Rs. {balance}\nThank you for choosing {store_name}.",
            ],
            [
                'key'     => 'job_alert',
                'label'   => 'Job Order Alert',
                'message' => "Hi {customer_name}, your device ({device}) status: *{status}*.\nOrder: {order_no} — {store_name}",
            ],
            [
                'key'     => 'field_alert',
                'label'   => 'Field Service Alert',
                'message' => "Hi {customer_name}, your field service request {complaint_no} has been updated: *{status}*.\nTech: {technician} — {store_name}",
            ],
            [
                'key'     => 'payment_reminder',
                'label'   => 'Payment Reminder',
                'message' => "Dear {customer_name}, a payment reminder for order {order_no}.\nBalance due: Rs. {balance}\nPlease contact us at {store_name}.",
            ],
        ];

        foreach ($templates as $t) {
            DB::table('whatsapp_templates')->insert(array_merge($t, [
                'active'     => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_templates');
        Schema::dropIfExists('whatsapp_settings');
    }
};
