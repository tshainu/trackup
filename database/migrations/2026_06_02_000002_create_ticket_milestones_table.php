<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable()->index();
            $table->unsignedBigInteger('field_complaint_id')->index();
            $table->string('title');
            $table->unsignedTinyInteger('order')->default(0);
            $table->enum('status', ['pending','in_progress','completed','skipped'])->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();       // assigned staff
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('transferred_to')->nullable(); // employee id
            $table->text('transfer_reason')->nullable();
            $table->boolean('help_requested')->default(false);
            $table->text('help_notes')->nullable();
            $table->timestamps();

            $table->foreign('field_complaint_id')->references('id')->on('field_complaints')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_milestones');
    }
};
