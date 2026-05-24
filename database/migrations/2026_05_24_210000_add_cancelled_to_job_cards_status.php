<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * This migration documents the manual SQLite schema fix:
     * Added 'Cancelled' to the job_cards.status CHECK constraint.
     * (SQLite doesn't support ALTER COLUMN — fix was applied directly via table rebuild)
     */
    public function up(): void
    {
        // Already applied directly to SQLite — no-op
    }

    public function down(): void
    {
        // Cannot easily revert SQLite CHECK constraint change
    }
};
