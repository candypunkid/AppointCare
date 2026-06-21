<?php

namespace Modules\Twilio\Services;

use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Modules\Appointment\Models\Appointment;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    private ?Client $twilio = null;

    /**
     * Lazy-load the Twilio Client using tenant-aware credentials if available.
     */
    private function getClient(Appointment $appointment = null): Client
    {
        if ($this->twilio) {
            return $this->twilio;
        }

        // Fallback logic for multi-tenancy as per Master Context
        $sid = config('services.twilio.account_sid');
        $token = config('services.twilio.auth_token');

        return $this->twilio = new Client($sid, $token);
    }

    /**
     * Initiate an outbound AI call (Prompt 04 implementation)
     */
    public function callCustomer(Appointment $appointment): string
    {
        try {
            $webhookUrl = config('services.twilio.webhook_base') . '/twilio/webhook/voice?appointment_id=' . $appointment->id;

            $call = $this->getClient($appointment)->calls->create(
                $appointment->customer_phone,
                config('services.twilio.phone_number'),
                [
                    'url' => $webhookUrl,
                    'method' => 'POST',
                    'statusCallback' => config('services.twilio.webhook_base') . '/twilio/webhook/status?appointment_id=' . $appointment->id,
                    'statusCallbackMethod' => 'POST',
                    'machineDetection' => 'Enable',
                    'record' => false,
                    'statusCallbackEvents' => ['initiated', 'ringing', 'answered', 'completed'],
                ]
            );

            $appointment->update([
                'twilio_call_sid' => $call->sid,
                'status' => 'calling',
                'call_attempts' => $appointment->call_attempts + 1,
                'last_called_at' => now(),
            ]);

            Log::channel('twilio')->info("Outbound call initiated", [
                'call_sid' => $call->sid,
                'appointment_id' => $appointment->id,
            ]);

            return $call->sid;
        } catch (\Exception $e) {
            Log::channel('twilio')->error("Failed to initiate call: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate TwiML for the voice stream (Prompt 04 requirement)
     */
    public function buildVoiceTwiML(Appointment $appointment): string
    {
        $response = new VoiceResponse();

        $greeting = "Hello " . $appointment->customer_name .
            ", I'm calling to confirm your " . $appointment->appointment_type .
            " appointment on " . $appointment->appointment_date->format('F jS') . ".";

        $response->say($greeting, ['voice' => 'Polly.Joanna']);

        $connect = $response->connect();
        $stream = $connect->stream([
            'url' => 'wss://' . request()->getHost() . '/ws/aicall/' . $appointment->id,
            'name' => 'openai-stream',
        ]);
        $stream->parameter(['name' => 'appointmentId', 'value' => (string)$appointment->id]);

        return $response->asXML();
    }

    /**
     * Handle voicemail detection
     */
    public function handleVoicemail(Appointment $appointment): string
    {
        $appointment->update(['status' => 'no_answer', 'notes' => 'Voicemail detected']);

        $response = new VoiceResponse();
        $response->say("Hello, this is an automated message to confirm your appointment. We will try to reach you again later. Goodbye.");
        $response->hangup();

        return $response->asXML();
    }

    /**
     * Send an SMS via Twilio
     */
    public function sendSms(string $to, string $message): void
    {
        try {
            $this->getClient()->messages->create($to, [
                'from' => config('services.twilio.phone_number'),
                'body' => $message
            ]);
        } catch (\Exception $e) {
            Log::channel('twilio')->error("SMS Failure: " . $e->getMessage());
        }
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
