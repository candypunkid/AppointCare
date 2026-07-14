<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            $table->string('twilio_call_sid')->unique()->nullable();
            $table->longText('transcript')->nullable();
            $table->string('detected_intent')->nullable();
            $table->longText('ai_response')->nullable();
            $table->string('recording_url')->nullable();
            $table->integer('duration')->nullable();
            $table->string('status')->default('initiated');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
