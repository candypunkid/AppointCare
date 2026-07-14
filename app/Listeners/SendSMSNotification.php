<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentRescheduled;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class SendSMSNotification
{
    public function __construct(
        protected TwilioService $twilioService
    ) {}

    public function handleConfirmed(AppointmentConfirmed $event): void
    {
        $customer = $event->appointment->customer;
        if (! $customer?->phone) {
            return;
        }

        $message = "AppointCare: Your appointment ({$event->appointment->service}) on {$event->appointment->scheduled_at->format('M d, Y \\a\\t g:i A')} has been CONFIRMED. Thank you!";

        $result = $this->twilioService->sendSMS($customer->phone, $message);

        if (! $result['success']) {
            Log::warning('SMS notification failed for confirmed appointment', [
                'appointment_id' => $event->appointment->id,
                'error' => $result['error'] ?? 'unknown',
            ]);
        }
    }

    public function handleCancelled(AppointmentCancelled $event): void
    {
        $customer = $event->appointment->customer;
        if (! $customer?->phone) {
            return;
        }

        $reason = $event->reason ? " Reason: {$event->reason}" : '';
        $message = "AppointCare: Your appointment ({$event->appointment->service}) on {$event->appointment->scheduled_at->format('M d, Y \\a\\t g:i A')} has been CANCELLED.{$reason}";

        $result = $this->twilioService->sendSMS($customer->phone, $message);

        if (! $result['success']) {
            Log::warning('SMS notification failed for cancelled appointment', [
                'appointment_id' => $event->appointment->id,
                'error' => $result['error'] ?? 'unknown',
            ]);
        }
    }

    public function handleRescheduled(AppointmentRescheduled $event): void
    {
        $customer = $event->appointment->customer;
        if (! $customer?->phone) {
            return;
        }

        $message = "AppointCare: Your appointment has been RESCHEDULED. New date: {$event->appointment->scheduled_at->format('M d, Y \\a\\t g:i A')}.";

        $result = $this->twilioService->sendSMS($customer->phone, $message);

        if (! $result['success']) {
            Log::warning('SMS notification failed for rescheduled appointment', [
                'appointment_id' => $event->appointment->id,
                'error' => $result['error'] ?? 'unknown',
            ]);
        }
    }
}
