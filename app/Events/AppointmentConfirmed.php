<?php

namespace App\Events;

use App\Models\Appointment;
use App\Models\CallLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppointmentConfirmed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public ?CallLog $callLog = null
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('appointments')];
    }

    public function broadcastAs(): string
    {
        return 'appointment.confirmed';
    }

    public function broadcastWith(): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'customer_name' => $this->appointment->customer?->name,
            'status' => 'confirmed',
        ];
    }
}
