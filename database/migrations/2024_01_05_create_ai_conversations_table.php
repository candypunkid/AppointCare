<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('appointment_request_id')->nullable()->constrained('appointment_requests')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->onDelete('set null');
            $table->string('customer_phone');
            $table->enum('conversation_type', ['voice', 'sms'])->default('voice');
            $table->string('twilio_call_sid')->nullable()->unique();
            $table->enum('status', ['initiated', 'in_progress', 'completed', 'failed'])->default('initiated');
            $table->longText('conversation_transcript')->nullable();
            $table->enum('action_taken', ['booked', 'cancelled', 'postponed', 'none'])->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->json('metadata')->nullable(); // store AI response data
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['customer_phone', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
