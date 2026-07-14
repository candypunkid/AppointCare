<?php

namespace App\Services;

use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Exception;
use Illuminate\Support\Facades\Log;

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

    public function initiateVoiceCall(string $phoneNumber, string $callbackUrl, ?string $statusCallbackUrl = null, ?string $appointmentId = null): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'Twilio is not configured.'];
        }

        try {
            $options = [
                'url' => $callbackUrl,
                'method' => 'POST',
                'statusCallbackEvent' => ['initiated', 'ringing', 'answered', 'completed'],
                'statusCallbackMethod' => 'POST',
                'machineDetection' => 'Enable',
            ];

            if ($statusCallbackUrl) {
                $options['statusCallback'] = $statusCallbackUrl;
            }

            if ($appointmentId) {
                $options['statusCallback'] = ($statusCallbackUrl ?: $callbackUrl) . '?appointment_id=' . $appointmentId;
            }

            $call = $this->twilio->calls->create(
                $phoneNumber,
                config('services.twilio.phone_number'),
                $options
            );

            return [
                'success' => true,
                'call_sid' => $call->sid,
                'status' => $call->status,
            ];
        } catch (Exception $e) {
            Log::error('Twilio outbound call failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendSMS(string $phoneNumber, string $message): array
    {
        if (! $this->isConfigured()) {
            return ['success' => false, 'error' => 'Twilio is not configured.'];
        }

        try {
            $sms = $this->twilio->messages->create(
                $phoneNumber,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $message,
                ]
            );

            return ['success' => true, 'message_sid' => $sms->sid, 'status' => $sms->status];
        } catch (Exception $e) {
            Log::error('Twilio SMS failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function generateAppointmentReminderTwiML(array $appointmentData, string $gatherUrl, string $humanTransferUrl = ''): VoiceResponse
    {
        $twiml = new VoiceResponse();

        $greeting = "Hello {$appointmentData['customer_name']}. This is AppointCare calling to remind you about your {$appointmentData['service']} appointment tomorrow at {$appointmentData['time']}. Will you be able to attend? Please say yes or no, or tell me how I can help you.";

        $twiml->say($greeting, ['voice' => 'Polly.Joanna', 'language' => 'en-US']);

        $twiml->gather([
            'input' => 'speech',
            'action' => $gatherUrl,
            'method' => 'POST',
            'timeout' => 10,
            'language' => 'en-US',
            'speechTimeout' => 'auto',
            'speechModel' => 'phone_call',
        ]);

        $twiml->redirect($gatherUrl);

        return $twiml;
    }

    public function generateSimpleResponseTwiML(string $message): VoiceResponse
    {
        $twiml = new VoiceResponse();
        $twiml->say($message, ['voice' => 'Polly.Joanna', 'language' => 'en-US']);
        return $twiml;
    }

    public function generateNepaliTwiML(string $message): VoiceResponse
    {
        $twiml = new VoiceResponse();
        $twiml->say($message, ['voice' => 'Polly.Joanna', 'language' => 'ne-NP']);
        return $twiml;
    }

    public function generateHangupTwiML(): VoiceResponse
    {
        $twiml = new VoiceResponse();
        $twiml->say('Thank you for using AppointCare. Goodbye.', ['voice' => 'Polly.Joanna']);
        $twiml->hangup();
        return $twiml;
    }

    public function generateDialTwiML(string $phoneNumber): VoiceResponse
    {
        $twiml = new VoiceResponse();
        $twiml->say('Please hold while I connect you to a representative.', ['voice' => 'Polly.Joanna']);
        $twiml->dial($phoneNumber);
        return $twiml;
    }

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
            ];
        } catch (Exception $e) {
            return null;
        }
    }

    public function getCallRecordingUrl(string $callSid): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        try {
            $recordings = $this->twilio->calls($callSid)->recordings->read();

            if (! empty($recordings)) {
                return $recordings[0]->url;
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function endCall(string $callSid): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        try {
            $this->twilio->calls($callSid)->update(['status' => 'completed']);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function createGatherUrlForAppointment(string $appointmentId): string
    {
        return route('api.twilio.voice') . '?appointment_id=' . $appointmentId;
    }

    public function createStatusUrlForAppointment(string $appointmentId): string
    {
        return route('api.twilio.status') . '?appointment_id=' . $appointmentId;
    }
}
