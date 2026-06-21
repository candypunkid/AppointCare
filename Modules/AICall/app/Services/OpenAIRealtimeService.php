<?php

namespace Modules\AICall\Services;

use WebSocket\Client as WsClient;
use Modules\Appointment\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIRealtimeService
{
    private ?WsClient $client = null;

    /**
     * Connect to OpenAI Realtime API and immediately send session config.
     */
    public function createClient(Appointment $appointment): WsClient
    {
        $wsUrl = "wss://api.openai.com/v1/realtime?model=" . config('aicall.openai.realtime_model', 'gpt-4o-realtime-preview');

        $this->client = new WsClient($wsUrl, [
            'headers' => [
                'Authorization' => 'Bearer ' . config('aicall.openai.api_key'),
                'OpenAI-Beta' => 'realtime=v1',
            ],
            'timeout' => 15
        ]);

        // Immediately configure session
        $this->client->send(json_encode($this->buildSessionConfig($appointment)));

        return $this->client;
    }

    /**
     * Build the session configuration specifically for Twilio G.711 compatibility.
     */
    private function buildSessionConfig(Appointment $appointment): array
    {
        $instructions = $this->getSystemInstructions() .
            "\nYou are speaking with " . $appointment->customer_name .
            " regarding their " . $appointment->appointment_type . " on " .
            $appointment->appointment_date->format('F j, Y at g:i A') . ".";

        return [
            'type' => 'session.update',
            'session' => [
                'modalities' => ['audio', 'text'],
                'instructions' => $instructions,
                'voice' => config('aicall.voice', 'alloy'),
                'input_audio_format' => 'g711_ulaw',
                'output_audio_format' => 'g711_ulaw',
                'input_audio_transcription' => [
                    'model' => 'whisper-1',
                ],
                'turn_detection' => [
                    'type' => 'server_vad',
                    'threshold' => 0.5,
                    'prefix_padding_ms' => 200,
                    'silence_duration_ms' => 800,
                ],
                'tool_choice' => 'auto',
                'tools' => $this->getTools(),
                'temperature' => 0.8,
            ],
        ];
    }

    /**
     * Get system instructions for the AI agent
     */
    private function getSystemInstructions(): string
    {
        return <<<'INSTRUCTIONS'
You are a professional appointment confirmation assistant. Your role is to:
1. Greet the customer warmly
2. Confirm their appointment details
3. Answer any questions about the appointment
4. Ensure they have all necessary information
5. Be concise and professional
6. If there are any issues, suggest rescheduling

Always be helpful and courteous. Keep responses brief (under 100 words per response).
INSTRUCTIONS;
    }

    public function getTools(): array
    {
        return [
            [
                'type' => 'function',
                'name' => 'confirm_appointment',
                'description' => 'Confirm the appointment as scheduled.',
                'parameters' => ['type' => 'object', 'properties' => (object)[]]
            ],
            [
                'type' => 'function',
                'name' => 'cancel_appointment',
                'description' => 'Cancel the appointment.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'reason' => ['type' => 'string', 'description' => 'The reason for cancellation']
                    ]
                ]
            ],
            [
                'type' => 'function',
                'name' => 'reschedule_appointment',
                'description' => 'Reschedule the appointment to a new date and time.',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'new_datetime' => ['type' => 'string', 'description' => 'ISO 8601 formatted date string'],
                        'reason' => ['type' => 'string']
                    ],
                    'required' => ['new_datetime']
                ],
            ],
            [
                'type' => 'function',
                'name' => 'end_call',
                'description' => 'End the phone call.',
                'parameters' => ['type' => 'object', 'properties' => (object)[]]
            ]
        ];
    }

    /**
     * Send audio data to AI
     */
    public function sendAudio(string $audioData): void
    {
        if (!$this->client) {
            throw new Exception('WebSocket client not connected');
        }

        $event = [
            'type' => 'input_audio_buffer.append',
            'audio' => base64_encode($audioData),
        ];

        $this->client->send(json_encode($event));
    }

    /**
     * Listen for responses from AI
     */
    public function listen(callable $onMessage, callable $onTranscript): void
    {
        if (!$this->client) {
            throw new Exception('WebSocket client not connected');
        }

        try {
            while ($this->client->isConnected()) {
                $message = $this->client->receive();
                $data = json_decode($message, true);

                if (isset($data['type'])) {
                    match ($data['type']) {
                        'response.text.delta' => $onMessage($data['delta'] ?? ''),
                        'conversation.item.input_audio_transcription.completed' =>
                        $onTranscript($data['transcript'] ?? ''),
                        'response.done' => $this->handleResponseDone($data),
                        default => null,
                    };
                }
            }
        } catch (Exception $e) {
            Log::error("Error listening to OpenAI stream: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle completion of response
     */
    private function handleResponseDone(array $data): void
    {
        Log::info("OpenAI response completed", [
            'stop_reason' => $data['response']['status'] ?? null,
        ]);
    }

    /**
     * Close the connection
     */
    public function disconnect(): void
    {
        if ($this->client && $this->client->isConnected()) {
            $this->client->close();
            Log::info("Disconnected from OpenAI Realtime API");
        }
    }

    /**
     * Get connection status
     */
    public function isConnected(): bool
    {
        return $this->client !== null && $this->client->isConnected();
    }
}
