<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        return [
            'tenant_id' => Tenant::factory(),
            'customer_id' => User::factory(),
            'staff_id' => null,
            'service' => fake()->randomElement(['Consultation', 'Checkup', 'Follow-up', 'Cleaning']),
            'scheduled_at' => now()->addDays(rand(1, 30))->setHour(rand(9, 16))->setMinute(0),
            'scheduled_end_at' => null,
            'status' => 'pending',
            'notes' => null,
            'metadata' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'confirmed']);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'cancelled']);
    }
}
