<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Admins
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('code')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Employees
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique();
            $table->string('employee_name');
            $table->string('registration_no')->nullable();
            $table->string('employee_address')->nullable();
            $table->string('nic')->nullable();
            $table->string('phone_no_1')->nullable();
            $table->string('phone_no_2')->nullable();
            $table->string('email')->nullable();
            $table->string('user_name')->unique();
            $table->string('role')->default('employee');
            $table->string('password');
            $table->string('code')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Device types (e.g., TV, Fan, AC)
        Schema::create('device_lists', function (Blueprint $table) {
            $table->id();
            $table->string('device_name');
            $table->timestamps();
        });

        // Device brands linked to device type
        Schema::create('device_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_list_id')->constrained('device_lists')->cascadeOnDelete();
            $table->string('device_brand');
            $table->timestamps();
        });

        // Device faults linked to device type
        Schema::create('device_faults', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_list_id')->constrained('device_lists')->cascadeOnDelete();
            $table->string('device_fault');
            $table->timestamps();
        });

        // Job cards (customer devices brought in for repair)
        Schema::create('job_cards', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->string('customer_id');
            $table->string('customer_name');
            $table->string('customer_address')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_nic')->nullable();
            $table->string('customer_dob')->nullable();
            $table->string('phone_no');
            $table->string('device_name');
            $table->string('device_brand')->nullable();
            $table->string('serial_no')->nullable();
            $table->string('device_age')->nullable();
            $table->string('device_fault')->nullable();
            $table->string('issue')->nullable();
            $table->date('date');
            $table->decimal('rupees', 10, 2)->default(0);
            $table->enum('status', ['Pending', 'In Progress', 'Completed', 'Not Completed'])->default('Pending');
            $table->string('remark')->nullable();
            $table->boolean('need_assistant')->default(false);
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        // Store/shop info
        Schema::create('store_info', function (Blueprint $table) {
            $table->id();
            $table->string('store_name');
            $table->string('registration_no')->nullable();
            $table->string('store_address')->nullable();
            $table->string('phone_no1')->nullable();
            $table->string('phone_no2')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_phoneno')->nullable();
            $table->string('owner_address')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_cards');
        Schema::dropIfExists('device_faults');
        Schema::dropIfExists('device_brands');
        Schema::dropIfExists('device_lists');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('store_info');
    }
};
