<?php

namespace Modules\Twilio\Services;

use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(config('twilio.account_sid'), config('twilio.auth_token'));
    }

    public function initiateCall($to, $appointmentId, $tenantSlug)
    {
        $webhookUrl = config('app.url') . "/twilio/webhook/stream?appointment_id={$appointmentId}";
        
        return $this->client->calls->create(
            $to,
            config('twilio.phone_number'),
            ['url' => $webhookUrl]
        );
    }

    public function generateStreamTwiML($appointmentId, $tenantSlug)
    {
        $response = new VoiceResponse();
        
        // 1. Initial Greeting
        $response->say("Please wait while I connect you to the appointment assistant.");

        // 2. Connect to the WebSocket Bridge
        $connect = $response->connect();
        $wssUrl = str_replace(['http://', 'https://'], ['ws://', 'wss://'], config('app.url')) . ":8080/voice";
        
        $stream = $connect->stream([
            'url' => $wssUrl,
            'name' => 'appointment_stream'
        ]);
        
        // Pass metadata to the stream
        $stream->parameter(['name' => 'appointment_id', 'value' => $appointmentId]);
        $stream->parameter(['name' => 'tenant_slug', 'value' => $tenantSlug]);

        $response->pause(['length' => 30]); // Keep the call alive while streaming
        
        return $response->__toString();
    }
}
