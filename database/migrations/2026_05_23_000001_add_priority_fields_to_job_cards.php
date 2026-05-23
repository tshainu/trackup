<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->string('priority')->default('Normal')->after('status');
            $table->date('estimated_delivery')->nullable()->after('priority');
            $table->text('accessories')->nullable()->after('estimated_delivery');
        });
    }
    public function down(): void {
        Schema::table('job_cards', function (Blueprint $table) {
            $table->dropColumn(['priority', 'estimated_delivery', 'accessories']);
        });
    }
};
