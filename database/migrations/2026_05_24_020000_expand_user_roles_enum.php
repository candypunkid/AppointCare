<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Add new enum values for roles (MySQL supports native ENUM)
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','tenant_admin','admin','staff','customer') NOT NULL DEFAULT 'customer'");

            return;
        }

        // SQLite (and other drivers): rebuild the column as a plain string so
        // the new role values are accepted.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 30)->default('customer')->after('phone');
        });
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            // Revert to previous enum values (admin, staff, customer)
            DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin','staff','customer') NOT NULL DEFAULT 'customer'");

            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'customer'])->default('customer')->after('phone');
        });
    }
};
