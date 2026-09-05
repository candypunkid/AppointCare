<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dateTime('original_scheduled_at')->nullable()->after('scheduled_at');
            $table->dateTime('rescheduled_at')->nullable()->after('original_scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn(['original_scheduled_at', 'rescheduled_at']);
        });
    }
};