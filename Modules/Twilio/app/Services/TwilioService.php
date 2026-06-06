<?php

namespace Modules\Twilio\Services;

use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Modules\Appointment\App\Models\Appointment;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    private Client $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );
    }

    /**
     * Initiate an outbound AI call
     */
    public function initiateCall(string $phoneNumber, int $appointmentId): string
    {
        try {
            $webhookUrl = config('services.twilio.webhook_base') . '/twilio/webhook/stream';

            $call = $this->twilio->calls->create(
                $phoneNumber,
                config('services.twilio.phone_number'),
                [
                    'url' => $webhookUrl,
                    'method' => 'POST',
                    'statusCallback' => config('services.twilio.webhook_base') . '/twilio/webhook/status',
                    'statusCallbackMethod' => 'POST',
                    'record' => false,
                    'statusCallbackEvents' => ['initiated', 'ringing', 'answered', 'completed'],
                ]
            );

            Log::info("Twilio call initiated", [
                'call_sid' => $call->sid,
                'phone_number' => $phoneNumber,
                'appointment_id' => $appointmentId,
            ]);

            return $call->sid;
        } catch (\Exception $e) {
            Log::error("Failed to initiate Twilio call: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate TwiML for streaming audio to OpenAI
     */
    public function generateStreamTwiML(int $appointmentId): string
    {
        $response = new VoiceResponse();

        $connect = $response->connect();
        $stream = $connect->stream([
            'url' => 'wss://openai-realtime-stream.example.com/stream',
            'name' => "appointment-{$appointmentId}",
        ]);

        return $response->asXML();
    }

    /**
     * Handle status callback
     */
    public function handleStatusCallback(string $callSid, string $callStatus, Appointment $appointment): void
    {
        Log::info("Call status update", [
            'call_sid' => $callSid,
            'status' => $callStatus,
            'appointment_id' => $appointment->id,
        ]);

        match ($callStatus) {
            'completed' => $this->handleCallCompleted($appointment),
            'failed' => $this->handleCallFailed($appointment),
            'no-answer' => $this->handleCallFailed($appointment),
            default => null,
        };
    }

    /**
     * Handle completed call
     */
    private function handleCallCompleted(Appointment $appointment): void
    {
        Log::info("Call completed for appointment {$appointment->id}");
        // The AppointmentStatusChanged event will handle notifications
    }

    /**
     * Handle failed call
     */
    private function handleCallFailed(Appointment $appointment): void
    {
        Log::warning("Call failed for appointment {$appointment->id}");
        $appointment->markAsFailed();
    }

    /**
     * Disconnect call
     */
    public function disconnectCall(string $callSid): void
    {
        try {
            $this->twilio->calls($callSid)->update(['status' => 'completed']);
            Log::info("Call disconnected", ['call_sid' => $callSid]);
        } catch (\Exception $e) {
            Log::error("Failed to disconnect call: " . $e->getMessage());
        }
    }
}
