<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add AI-related statuses (rescheduled, transferred, failed) to the
     * appointments.status ENUM on MySQL. SQLite rebuilds the schema from the
     * base migration during tests, so it needs no change here.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `appointments` MODIFY `status` ENUM('pending','confirmed','in_progress','completed','cancelled','postponed','rescheduled','transferred','failed') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE `appointments` MODIFY `status` ENUM('pending','confirmed','in_progress','completed','cancelled','postponed') NOT NULL DEFAULT 'pending'");
    }
};
