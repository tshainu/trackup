<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires re-creating the enum to add values
        DB::statement("ALTER TABLE cctv_surveys MODIFY COLUMN status ENUM('Scheduled','Completed','Cancelled','Need More Time') NOT NULL DEFAULT 'Scheduled'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cctv_surveys MODIFY COLUMN status ENUM('Scheduled','Completed','Cancelled') NOT NULL DEFAULT 'Scheduled'");
    }
};
