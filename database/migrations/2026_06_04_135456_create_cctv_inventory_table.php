<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('item_code', 50)->nullable();
            $table->string('name', 200);
            $table->enum('category', ['Camera', 'DVR', 'NVR', 'HDD', 'Cable', 'Connector', 'Power Supply', 'UPS', 'Switch', 'Router', 'Other']);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->integer('qty_in_stock')->default(0);
            $table->integer('low_stock_alert')->default(5);
            $table->decimal('unit_cost', 10, 2)->default(0);
            $table->decimal('selling_price', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cctv_inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->unsignedBigInteger('inventory_id');
            $table->enum('type', ['in', 'out']);
            $table->integer('qty');
            $table->string('reference', 100)->nullable(); // project_no, repair_no etc
            $table->string('note', 300)->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_inventory_logs');
        Schema::dropIfExists('cctv_inventory');
    }
};
