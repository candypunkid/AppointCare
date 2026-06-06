<?php

namespace Modules\Notification\Listeners;

use Modules\Appointment\Events\AppointmentStatusChanged;
use Modules\Notification\Notifications\AppointmentStatusNotification;
use Modules\Twilio\Services\TwilioService;
use Illuminate\Support\Facades\Notification;

class SendAppointmentNotification
{
    public function __construct(
        private TwilioService $twilioService
    ) {}

    /**
     * Handle the event
     */
    public function handle(AppointmentStatusChanged $event): void
    {
        $appointment = $event->appointment;

        // Create a temporary notifiable object for the customer
        $notifiable = new \stdClass();
        $notifiable->name = $appointment->customer_name;
        $notifiable->email = $appointment->customer_email;
        $notifiable->phone = $appointment->customer_phone;

        // Send notification
        Notification::send($notifiable, new AppointmentStatusNotification($appointment));

        \Illuminate\Support\Facades\Log::info("Notification sent for appointment {$appointment->id}", [
            'status' => $appointment->status,
            'email' => $appointment->customer_email,
            'phone' => $appointment->customer_phone,
        ]);
    }
}
