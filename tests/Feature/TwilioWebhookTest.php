<?php

namespace Tests\Feature;

use App\Models\AiAction;
use App\Models\Appointment;
use App\Models\CallLog;
use App\Models\User;
use App\Services\OpenAIService;
use App\Services\TwilioService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
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

        $response = $this->post('/api/twilio/voice?appointment_id='.$appointment->id, [
            'CallSid' => 'CA'.str_repeat('0', 32),
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<Response>', false);
        $response->assertSee('<Say', false);
    }

    public function test_voice_webhook_without_call_sid_does_not_500(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'phone' => '+9779840000000']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'service' => 'Consultation',
            'scheduled_at' => now()->addDay()->setHour(14)->setMinute(0),
            'status' => 'pending',
        ]);

        $response = $this->post('/api/twilio/voice?appointment_id='.$appointment->id);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<Say', false);
    }

    public function test_status_webhook_handles_completed_call(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'in_progress',
        ]);

        $callSid = 'CA-test-status-'.Str::random(10);

        $response = $this->post('/api/twilio/status?appointment_id='.$appointment->id, [
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

        $response = $this->post('/api/twilio/outbound-call?appointment_id='.$appointment->id, [
            'CallSid' => 'CA'.str_repeat('1', 32),
        ]);

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
    }

    public function test_speech_turn_is_processed_exactly_once(): void
    {
        $customer = User::factory()->create(['role' => 'customer', 'phone' => '+9779840000000']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'service' => 'Consultation',
            'scheduled_at' => now()->addDay()->setHour(14)->setMinute(0),
            'status' => 'pending',
        ]);

        $this->mock(OpenAIService::class, function ($mock) {
            $mock->shouldReceive('analyzeIntent')->once()->andReturn([
                'intent' => 'confirm_appointment',
                'confidence' => 0.95,
                'response_message' => 'Great! Your appointment has been confirmed.',
                'new_date' => '',
                'new_time' => '',
            ]);
        });

        $this->partialMock(TwilioService::class, function ($mock) {
            $mock->shouldReceive('sendSMS')->andReturn(['success' => false, 'error' => 'skipped in tests']);
        });

        $callSid = 'CA'.Str::random(20);

        $response = $this->post('/api/twilio/voice?appointment_id='.$appointment->id, [
            'CallSid' => $callSid,
            'SpeechResult' => 'Yes, I will attend',
        ]);

        $response->assertOk();

        $this->assertSame('confirmed', $appointment->fresh()->status);

        $callLog = CallLog::where('twilio_call_sid', $callSid)->first();
        $this->assertNotNull($callLog);
        $this->assertSame(1, $callLog->conversationLogs()->where('speaker', 'ai')->count());
        $this->assertSame(1, AiAction::where('appointment_id', $appointment->id)->count());
    }
}
