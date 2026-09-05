<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\CallLog;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConversationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ConversationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ConversationService::class);
    }

    public function test_it_creates_call_log(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);

        $callLog = $this->service->startCallLog($appointment->id, 'CA'.str_repeat('0', 32));

        $this->assertInstanceOf(CallLog::class, $callLog);
        $this->assertEquals($appointment->id, $callLog->appointment_id);
        $this->assertEquals('in_progress', $callLog->status);
    }

    public function test_it_appends_messages(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create(['customer_id' => $customer->id]);
        $callLog = $this->service->startCallLog($appointment->id, 'CA-test');

        $log = $this->service->appendMessage($callLog, 'customer', 'Yes, I can attend');

        $this->assertDatabaseHas('conversation_logs', [
            'call_log_id' => $callLog->id,
            'speaker' => 'customer',
            'message' => 'Yes, I can attend',
        ]);
    }

    public function test_it_completes_call_log(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'in_progress',
        ]);
        $callLog = $this->service->startCallLog($appointment->id, 'CA-test');

        $this->service->completeCall($callLog, 'completed');

        $this->assertEquals('completed', $callLog->fresh()->status);
    }
}
