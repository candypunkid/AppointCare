<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->dateTime('appointment_date');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('twilio_call_sid')->nullable();
            $table->text('call_transcript')->nullable();
            $table->text('ai_summary')->nullable();
            $table->string('appointment_type')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('appointment_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
