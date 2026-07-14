<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('call_log_id')->constrained()->onDelete('cascade');
            $table->string('speaker');
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_logs');
    }
};
