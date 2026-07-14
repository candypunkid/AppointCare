<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentAIControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_analyze_intent_endpoint_validates_request(): void
    {
        $response = $this->postJson('/api/openai/analyze', []);

        $response->assertStatus(422);
    }

    public function test_analyze_intent_returns_analysis(): void
    {
        $response = $this->postJson('/api/openai/analyze', [
            'conversation' => [
                ['speaker' => 'ai', 'message' => 'Hello, can you confirm your appointment?'],
                ['speaker' => 'customer', 'message' => 'Yes, I will be there.'],
            ],
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['intent', 'confidence', 'response_message', 'new_date', 'new_time'],
            ]);
    }

    public function test_availability_check_requires_date(): void
    {
        $response = $this->getJson('/api/ai/appointments/availability');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_check_availability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->getJson('/api/ai/appointments/availability?date=' . now()->addDay()->format('Y-m-d'));

        $response->assertOk()
            ->assertJsonStructure(['success', 'data' => ['available', 'existing_count', 'max_slots']]);
    }

    public function test_authenticated_user_can_get_available_slots(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->getJson('/api/ai/appointments/slots?date=' . now()->addDay()->format('Y-m-d'));

        $response->assertOk()
            ->assertJsonStructure(['success', 'data' => ['date', 'available_slots']]);
    }

    public function test_authenticated_user_can_trigger_reminder_call(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer', 'phone' => '+9779840000000']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->postJson('/api/ai/appointments/initiate-call', [
            'appointment_id' => $appointment->id,
        ]);

        $response->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_dashboard_analytics_returns_data(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->getJson('/api/dashboard/analytics');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data' => [
                'total_calls', 'confirmed', 'cancelled', 'rescheduled',
                'no_shows', 'average_call_duration_seconds', 'ai_accuracy_percent',
            ]]);
    }
}
