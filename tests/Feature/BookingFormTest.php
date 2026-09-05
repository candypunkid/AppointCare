<?php

namespace Tests\Feature;

use App\Jobs\MakeReminderCallJob;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class BookingFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_form_creates_appointment_and_queues_ai_call(): void
    {
        Queue::fake();

        $tenant = Tenant::factory()->create(['is_active' => true]);

        $response = $this->postJson('/api/book-and-call', [
            'customer_name' => 'Ram Sharma',
            'customer_email' => 'ram@example.com',
            'customer_phone' => '9841234567',
            'appointment_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'service' => 'Consultation',
            'description' => 'Needs a checkup',
        ]);

        $response->assertCreated()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'ram@example.com',
            'role' => 'customer',
            'phone' => '+9779841234567',
        ]);

        $this->assertDatabaseHas('appointments', [
            'tenant_id' => $tenant->id,
            'status' => 'pending',
            'service' => 'Consultation',
        ]);

        Queue::assertPushed(MakeReminderCallJob::class);
    }

    public function test_booking_form_page_renders(): void
    {
        Tenant::create([
            'name' => 'AppointCare',
            'slug' => 'appointcare',
            'domain' => 'appointcare.local',
            'is_active' => true,
        ]);

        $this->get('/booking')->assertOk()->assertSee('Book an Appointment');
    }

    public function test_call_simulator_page_renders(): void
    {
        Tenant::create([
            'name' => 'AppointCare',
            'slug' => 'appointcare',
            'domain' => 'appointcare.local',
            'is_active' => true,
        ]);

        $this->get('/call-simulator')->assertOk()->assertSee('AI Call Simulator');
    }

    public function test_booking_form_validation_rejects_bad_payload(): void
    {
        $this->postJson('/api/book-and-call', [
            'customer_name' => '',
            'customer_email' => 'not-an-email',
            'customer_phone' => '',
            'appointment_date' => now()->subDay()->format('Y-m-d H:i:s'),
        ])->assertUnprocessable();
    }
}
