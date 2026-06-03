<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add new enum values for roles
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','tenant_admin','admin','staff','customer') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        // Revert to previous enum values (admin, staff, customer)
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','staff','customer') NOT NULL DEFAULT 'customer'");
    }
};
