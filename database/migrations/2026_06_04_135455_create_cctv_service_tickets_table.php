<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cctv_service_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->string('ticket_no', 30)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name', 150);
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->enum('ticket_type', ['Camera Fault', 'DVR Fault', 'HDD Failure', 'Recording Issue', 'Network Issue', 'Power Issue', 'Image Quality Issue', 'General Support', 'Other'])->default('General Support');
            $table->text('complaint_details')->nullable();
            $table->enum('priority', ['Low', 'Normal', 'High', 'Urgent'])->default('Normal');
            $table->unsignedBigInteger('assigned_technician')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('ticket_source', ['AMC', 'On-The-Go', 'Warranty'])->default('On-The-Go');
            $table->unsignedBigInteger('amc_id')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->string('signature_path')->nullable();
            $table->enum('status', ['Open', 'Assigned', 'In Progress', 'Waiting Parts', 'Completed', 'Closed'])->default('Open');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('cctv_service_tickets'); }
};
