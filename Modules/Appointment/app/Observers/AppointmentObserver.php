<?php

namespace Modules\Appointment\Observers;

use Illuminate\Support\Facades\Log;
use Modules\Appointment\Models\Appointment;

class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        Log::info("Appointment created: {$appointment->id}", [
            'customer' => $appointment->customer_name,
            'email' => $appointment->customer_email,
        ]);
    }

    public function updated(Appointment $appointment): void
    {
        if ($appointment->wasChanged('status')) {
            Log::info("Appointment status changed: {$appointment->id}", [
                'old_status' => $appointment->getOriginal('status'),
                'new_status' => $appointment->status,
            ]);
        }
    }

    public function deleted(Appointment $appointment): void
    {
        Log::info("Appointment soft deleted: {$appointment->id}");
    }
}
