<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('field_complaints', function (Blueprint $table) {
            $table->foreignId('customer_db_id')
                  ->nullable()
                  ->after('complaint_no')
                  ->constrained('customers')
                  ->nullOnDelete();
            // GPS on field_complaints (can be set independently per visit)
            $table->decimal('gps_lat', 10, 7)->nullable()->after('location_notes');
            $table->decimal('gps_lng', 10, 7)->nullable()->after('gps_lat');
            $table->string('gps_label')->nullable()->after('gps_lng');
        });
    }

    public function down(): void
    {
        Schema::table('field_complaints', function (Blueprint $table) {
            $table->dropForeign(['customer_db_id']);
            $table->dropColumn(['customer_db_id','gps_lat','gps_lng','gps_label']);
        });
    }
};
