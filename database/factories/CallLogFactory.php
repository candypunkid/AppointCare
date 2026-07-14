<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\CallLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallLogFactory extends Factory
{
    protected $model = CallLog::class;

    public function definition(): array
    {
        return [
            'appointment_id' => Appointment::factory(),
            'twilio_call_sid' => 'CA' . fake()->regexify('[A-Za-z0-9]{32}'),
            'transcript' => null,
            'detected_intent' => null,
            'ai_response' => null,
            'recording_url' => null,
            'duration' => null,
            'status' => 'initiated',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'duration' => rand(30, 300),
            'detected_intent' => 'confirm_appointment',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'failed']);
    }
}
