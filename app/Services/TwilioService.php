<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\Twiml\VoiceResponse;
use Exception;

class TwilioService
{
    protected ?Client $twilio = null;

    public function __construct()
    {
        if (! class_exists(Client::class)) {
            return;
        }

        $this->twilio = new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );
    }

    protected function isConfigured(): bool
    {
        return $this->twilio instanceof Client;
    }

    /**
     * Initiate a voice call to a customer.
     */
    public function initiateVoiceCall(string $phoneNumber, string $callbackUrl): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio SDK is not installed or configured.',
            ];
        }

        try {
            $call = $this->twilio->calls->create(
                $phoneNumber,
                config('services.twilio.phone_number'),
                [
                    'url' => $callbackUrl,
                    'method' => 'POST',
                    'statusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
                    'statusCallback' => route('twilio.call-status'),
                    'statusCallbackMethod' => 'POST',
                ]
            );

            return [
                'success' => true,
                'call_sid' => $call->sid,
                'status' => $call->status,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send an SMS to customer.
     */
    public function sendSMS(string $phoneNumber, string $message): array
    {
        if (! $this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio SDK is not installed or configured.',
            ];
        }

        try {
            $sms = $this->twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $message,
                ]
            );

            return [
                'success' => true,
                'message_sid' => $sms->sid,
                'status' => $sms->status,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get call recording URL.
     */
    public function getCallRecordingUrl(string $callSid): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $call = $this->twilio->calls($callSid)->fetch();
            return $call->recordingUrl;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Generate TwiML for AI appointment booking conversation.
     */
    public function generateAppointmentBookingTwiML(string $tenantName, string $gatherUrl): VoiceResponse
    {
        $twiml = new VoiceResponse();

        $twiml->say(
            "Hello! I'm calling from $tenantName. I'm an AI assistant here to help you book, cancel, or reschedule an appointment. " .
                "Press 1 to book an appointment, press 2 to cancel, or press 3 to postpone.",
            ['voice' => 'alice', 'language' => 'en-US']
        );

        $twiml->gather(
            [
                'numDigits' => 1,
                'action' => $gatherUrl,
                'method' => 'POST',
                'timeout' => 5,
            ]
        );

        return $twiml;
    }

    /**
     * Get call details.
     */
    public function getCallDetails(string $callSid): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $call = $this->twilio->calls($callSid)->fetch();

            return [
                'sid' => $call->sid,
                'status' => $call->status,
                'duration' => $call->duration,
                'start_time' => $call->startTime,
                'end_time' => $call->endTime,
                'direction' => $call->direction,
                'from' => $call->from,
                'to' => $call->to,
                'recording_url' => $call->recordingUrl,
            ];
        } catch (Exception $e) {
            return null;
        }
    }
}
