<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('label_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id');
            $table->float('width_mm')->default(62);
            $table->float('height_mm')->default(29);
            $table->integer('font_size')->default(10);
            $table->timestamps();
            $table->unique('shop_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('label_settings');
    }
};
