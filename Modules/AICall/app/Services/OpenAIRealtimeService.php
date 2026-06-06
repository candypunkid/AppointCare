<?php

namespace Modules\AICall\Services;

use WebSocket\Client as WsClient;
use Modules\Appointment\Models\Appointment;
use Illuminate\Support\Facades\Log;
use Exception;

class OpenAIRealtimeService
{
    private ?WsClient $client = null;
    private array $sessionConfig;

    public function __construct()
    {
        $this->sessionConfig = [
            'type' => 'session.update',
            'session' => [
                'modalities' => ['text', 'audio'],
                'instructions' => $this->getSystemInstructions(),
                'voice' => 'alloy',
                'input_audio_format' => config('aicall.openai.input_audio_format'),
                'output_audio_format' => config('aicall.openai.output_audio_format'),
                'input_audio_transcription' => [
                    'model' => 'whisper-1',
                ],
                'turn_detection' => [
                    'type' => 'server_vad',
                    'threshold' => 0.5,
                    'prefix_padding_ms' => 300,
                    'silence_duration_ms' => 500,
                ],
                'temperature' => 0.8,
                'max_response_output_tokens' => 4096,
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

    /**
     * Connect to OpenAI Realtime API and start the session
     */
    public function connect(Appointment $appointment): void
    {
        try {
            $wsUrl = config('aicall.openai.websocket_url') . '?model=' . config('aicall.openai.realtime_model');

            $this->client = new WsClient($wsUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('aicall.openai.api_key'),
                    'OpenAI-Beta' => 'realtime=v1',
                ],
            ]);

            Log::info("Connected to OpenAI Realtime API for appointment {$appointment->id}");

            // Send session configuration
            $this->client->send(json_encode($this->sessionConfig));

            // Send initial greeting
            $this->sendMessage("Hello! I'm calling to confirm your appointment on " . $appointment->appointment_date->format('F j, Y \a\t g:i A') . ". Is this a good time to talk?");
        } catch (Exception $e) {
            Log::error("Failed to connect to OpenAI Realtime API: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send text message to AI
     */
    public function sendMessage(string $message): void
    {
        if (!$this->client) {
            throw new Exception('WebSocket client not connected');
        }

        $event = [
            'type' => 'conversation.item.create',
            'item' => [
                'type' => 'message',
                'role' => 'user',
                'content' => [
                    [
                        'type' => 'text',
                        'text' => $message,
                    ],
                ],
            ],
        ];

        $this->client->send(json_encode($event));
        Log::debug("Sent message to OpenAI: " . substr($message, 0, 100));
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
