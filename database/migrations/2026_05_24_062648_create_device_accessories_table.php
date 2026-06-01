<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_accessories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_list_id')->constrained('device_lists')->onDelete('cascade');
            $table->string('accessory_name', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_accessories');
    }
};
