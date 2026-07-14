<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TwilioWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_voice_webhook_returns_twiml(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'phone' => '+9779840000000']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'service' => 'Consultation',
            'scheduled_at' => now()->addDay()->setHour(14)->setMinute(0),
            'status' => 'pending',
        ]);

        $response = $this->post('/api/twilio/voice?appointment_id=' . $appointment->id, [
            'CallSid' => 'CA' . str_repeat('0', 32),
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<Response>');
        $response->assertSee('<Say');
    }

    public function test_status_webhook_handles_completed_call(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'in_progress',
        ]);

        $callSid = 'CA-test-status-' . str_random(10);

        $response = $this->post('/api/twilio/status?appointment_id=' . $appointment->id, [
            'CallSid' => $callSid,
            'CallStatus' => 'completed',
            'CallDuration' => '45',
        ]);

        $response->assertOk();
    }

    public function test_outbound_call_endpoint_returns_twiml(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'name' => 'John Doe', 'phone' => '+9779840000000']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'service' => 'Checkup',
            'scheduled_at' => now()->addDay()->setHour(10)->setMinute(0),
            'status' => 'pending',
        ]);

        $response = $this->post('/api/twilio/outbound-call?appointment_id=' . $appointment->id, [
            'CallSid' => 'CA' . str_repeat('1', 32),
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
    }
}
