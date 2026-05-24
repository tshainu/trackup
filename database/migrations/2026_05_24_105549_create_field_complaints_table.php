<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('field_complaints', function (Blueprint $table) {
            $table->id();
            $table->string('complaint_no')->unique();       // FC-2605001
            $table->string('customer_name');
            $table->string('phone_no');
            $table->text('address');
            $table->text('location_notes')->nullable();
            $table->foreignId('service_type_id')->nullable()->constrained('service_types')->nullOnDelete();
            $table->string('service_type_name')->nullable(); // snapshot
            $table->text('description')->nullable();         // fault description
            $table->enum('priority', ['Low','Normal','High','Urgent'])->default('Normal');
            $table->enum('status', ['Pending','Assigned','In Progress','Completed','Billed','Cancelled'])->default('Pending');
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();   // field staff notes on completion
            $table->json('photos')->nullable();             // for future mobile app
            $table->decimal('service_charge', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('advance_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid','partial','paid'])->default('unpaid');
            $table->boolean('payment_received')->default(false);
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // employee id who logged it
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_complaints');
    }
};
