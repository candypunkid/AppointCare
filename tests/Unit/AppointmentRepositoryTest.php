<?php

namespace Tests\Unit;

use App\Models\Appointment;
use App\Models\User;
use App\Repositories\AppointmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppointmentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AppointmentRepository::class);
    }

    public function test_it_finds_appointment_by_id(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $appointment = Appointment::factory()->create(['customer_id' => $customer->id]);

        $found = $this->repository->findById($appointment->id);

        $this->assertNotNull($found);
        $this->assertEquals($appointment->id, $found->id);
    }

    public function test_it_returns_null_for_nonexistent_appointment(): void
    {
        $found = $this->repository->findById(999);
        $this->assertNull($found);
    }

    public function test_it_returns_analytics_structure(): void
    {
        $analytics = $this->repository->getAnalytics();

        $this->assertArrayHasKey('total_calls', $analytics);
        $this->assertArrayHasKey('confirmed', $analytics);
        $this->assertArrayHasKey('cancelled', $analytics);
        $this->assertArrayHasKey('rescheduled', $analytics);
        $this->assertArrayHasKey('no_shows', $analytics);
        $this->assertArrayHasKey('average_call_duration_seconds', $analytics);
        $this->assertArrayHasKey('ai_accuracy_percent', $analytics);
    }

    public function test_it_paginates_appointments(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        Appointment::factory()->count(5)->create(['customer_id' => $customer->id]);

        $result = $this->repository->paginate(15);

        $this->assertCount(5, $result->items());
    }
}
