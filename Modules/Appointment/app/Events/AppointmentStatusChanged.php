<?php

namespace Modules\Appointment\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\Appointment\Models\Appointment;

class AppointmentStatusChanged implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('appointments'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'appointment_id' => $this->appointment->id,
            'status' => $this->appointment->status,
            'updated_at' => $this->appointment->updated_at,
        ];
    }
}
