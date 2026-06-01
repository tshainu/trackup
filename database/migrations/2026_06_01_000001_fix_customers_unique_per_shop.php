<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_customer_id_unique');
            $table->dropUnique('customers_phone_unique');
            // customer_id unique per shop
            $table->unique(['shop_id', 'customer_id'], 'customers_shop_customer_id_unique');
            // phone unique per shop
            $table->unique(['shop_id', 'phone'], 'customers_shop_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_shop_customer_id_unique');
            $table->dropUnique('customers_shop_phone_unique');
            $table->unique('customer_id');
            $table->unique('phone');
        });
    }
};
