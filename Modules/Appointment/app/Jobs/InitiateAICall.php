<?php

namespace Modules\Appointment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Appointment\Models\Appointment;
use Modules\Twilio\Services\TwilioService;

class InitiateAICall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment
    ) {
        $this->queue = 'calls';
        $this->timeout = 600;
        $this->tries = 3;
    }

    public function handle(TwilioService $twilioService): void
    {
        try {
            Log::info("Initiating AI call for appointment {$this->appointment->id}");

            // Call Twilio service to initiate the call
            $callSid = $twilioService->initiateCall(
                $this->appointment->customer_phone,
                $this->appointment->id
            );

            // Update appointment with Twilio call SID
            $this->appointment->update([
                'twilio_call_sid' => $callSid,
                'status' => 'confirmed',
            ]);

            Log::info('AI call initiated successfully', ['call_sid' => $callSid]);
        } catch (\Exception $e) {
            Log::error('Failed to initiate AI call: '.$e->getMessage());
            $this->appointment->markAsFailed();
            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error("InitiateAICall job failed permanently for appointment {$this->appointment->id}", [
            'error' => $exception->getMessage(),
        ]);
        $this->appointment->markAsFailed();
    }
}
