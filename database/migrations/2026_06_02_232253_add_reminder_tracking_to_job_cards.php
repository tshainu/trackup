<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->unsignedTinyInteger('reminder_sent_count')->default(0)->after('payment_received');
            $table->timestamp('last_reminder_sent_at')->nullable()->after('reminder_sent_count');
        });
    }

    public function down(): void
    {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn(['reminder_sent_count', 'last_reminder_sent_at']);
        });
    }
};
