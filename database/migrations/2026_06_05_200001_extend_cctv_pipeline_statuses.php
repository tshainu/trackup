<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Lead statuses
        DB::statement("ALTER TABLE cctv_leads MODIFY COLUMN status ENUM(
            'New Lead','Survey Scheduled','Survey Completed','Quotation Sent',
            'Approved','Installation','Completed','Cancelled','Rejected','Postponed','Rescheduled','Lost'
        ) NOT NULL DEFAULT 'New Lead'");

        // Survey statuses
        DB::statement("ALTER TABLE cctv_surveys MODIFY COLUMN status ENUM(
            'Scheduled','Completed','Cancelled','Need More Time','Postponed','Rescheduled'
        ) NOT NULL DEFAULT 'Scheduled'");

        // Quotation statuses
        DB::statement("ALTER TABLE cctv_quotations MODIFY COLUMN status ENUM(
            'Draft','Sent','Approved','Rejected','Postponed','Rescheduled'
        ) NOT NULL DEFAULT 'Draft'");

        // Project status (it's a VARCHAR so just ensure values are handled in app)
        // Add invoice_id FK
        DB::statement("ALTER TABLE cctv_projects ADD COLUMN invoice_id BIGINT UNSIGNED NULL AFTER quotation_id");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cctv_leads MODIFY COLUMN status ENUM('New Lead','Survey Scheduled','Survey Completed','Quotation Sent','Approved','Lost') NOT NULL DEFAULT 'New Lead'");
        DB::statement("ALTER TABLE cctv_surveys MODIFY COLUMN status ENUM('Scheduled','Completed','Cancelled','Need More Time') NOT NULL DEFAULT 'Scheduled'");
        DB::statement("ALTER TABLE cctv_quotations MODIFY COLUMN status ENUM('Draft','Sent','Approved','Rejected') NOT NULL DEFAULT 'Draft'");
        DB::statement("ALTER TABLE cctv_projects DROP COLUMN IF EXISTS invoice_id");
    }
};
