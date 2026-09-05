<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\CallLog;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AppointmentService::class);
    }

    public function test_it_confirms_appointment(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        $callLog = CallLog::factory()->create(['appointment_id' => $appointment->id]);

        $this->service->confirmAppointment($appointment, $callLog, 0.95);

        $this->assertEquals('confirmed', $appointment->fresh()->status);
        $this->assertDatabaseHas('ai_actions', [
            'appointment_id' => $appointment->id,
            'action' => 'confirm',
        ]);
    }

    public function test_it_cancels_appointment(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
        ]);
        $callLog = CallLog::factory()->create(['appointment_id' => $appointment->id]);

        $this->service->cancelAppointment($appointment, $callLog, 0.9, 'Customer is busy');

        $this->assertEquals('cancelled', $appointment->fresh()->status);
        $this->assertStringContainsString('Customer is busy', $appointment->fresh()->notes);
    }

    public function test_it_reschedules_appointment(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'scheduled_at' => now()->addDay(),
        ]);
        $callLog = CallLog::factory()->create(['appointment_id' => $appointment->id]);

        $this->service->rescheduleAppointment($appointment, $callLog, 0.85, '2026-06-28', '15:00');

        $fresh = $appointment->fresh();
        $this->assertEquals('rescheduled', $fresh->status);
        $this->assertEquals('2026-06-28 15:00:00', $fresh->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertEquals('2026-06-28 15:00:00', $fresh->rescheduled_at->format('Y-m-d H:i:s'));
        $this->assertNotNull($fresh->original_scheduled_at);
    }

    public function test_it_does_not_reschedule_when_no_date_provided(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $scheduledAt = now()->addDay();
        $appointment = Appointment::factory()->create([
            'customer_id' => $customer->id,
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
        ]);
        $callLog = CallLog::factory()->create(['appointment_id' => $appointment->id]);

        $this->service->rescheduleAppointment($appointment, $callLog, 0.85, '', '');

        $fresh = $appointment->fresh();
        $this->assertEquals('pending', $fresh->status);
        $this->assertEquals($scheduledAt->format('Y-m-d H:i:s'), $fresh->scheduled_at->format('Y-m-d H:i:s'));
        $this->assertNull($fresh->rescheduled_at);
        $this->assertNull($fresh->original_scheduled_at);
    }

    public function test_it_checks_slot_availability(): void
    {
        $result = $this->service->checkSlotAvailability(now()->addDay()->format('Y-m-d'), '10:00');

        $this->assertArrayHasKey('available', $result);
        $this->assertArrayHasKey('existing_count', $result);
        $this->assertArrayHasKey('max_slots', $result);
    }
}
