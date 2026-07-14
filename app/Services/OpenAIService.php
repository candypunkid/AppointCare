<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    protected mixed $client;
    protected string $model;
    protected bool $mock;

    public function __construct()
    {
        $apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->mock = false;

        if ($apiKey) {
            $this->client = \OpenAI::client($apiKey);
        } else {
            $this->mock = (bool) env('AI_MOCK', true);
        }
    }

    public function isAvailable(): bool
    {
        return ! empty($this->client) || $this->mock;
    }

    public function chat(string $prompt, array $options = []): string
    {
        if (! $this->isAvailable()) {
            throw new Exception('AI client not configured.');
        }

        if ($this->mock && empty($this->client)) {
            return $this->mockResponse($prompt);
        }

        $system = $options['system'] ?? 'You are a helpful assistant for AppointCare.';

        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $options['max_tokens'] ?? 500,
            'temperature' => $options['temperature'] ?? 0.3,
            'response_format' => $options['response_format'] ?? null,
        ]);

        $choice = $response->choices[0] ?? null;

        return $choice?->message->content ?? '';
    }

    public function analyzeIntent(array $conversationHistory, array $appointmentData): array
    {
        $systemPrompt = $this->buildSystemPrompt();
        $userPrompt = $this->buildUserPrompt($conversationHistory, $appointmentData);

        try {
            $response = $this->chat($userPrompt, [
                'system' => $systemPrompt,
                'temperature' => 0.2,
                'max_tokens' => 600,
            ]);

            $cleaned = trim($response);
            if (str_starts_with($cleaned, '```')) {
                $cleaned = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $cleaned);
            }

            $result = json_decode($cleaned, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning('OpenAI returned non-JSON response', ['response' => $response]);
                return $this->defaultIntent();
            }

            return array_merge($this->defaultIntent(), $result);
        } catch (Exception $e) {
            Log::error('OpenAI intent analysis failed', ['error' => $e->getMessage()]);
            return $this->defaultIntent();
        }
    }

    protected function buildSystemPrompt(): string
    {
        return <<<PROMPT
You are an AI receptionist for AppointCare, an appointment management system.
Your role is to analyze customer responses during appointment reminder calls.

Supported intents:
- confirm_appointment: Customer confirms they will attend
- cancel_appointment: Customer wants to cancel
- reschedule_appointment: Customer wants to change date/time
- ask_question: Customer has a question about their appointment
- transfer_to_human: Customer wants to speak with a person
- unknown: Cannot determine intent

Rules:
1. Detect intent from the conversation history
2. For reschedule, extract new_date (YYYY-MM-DD) and new_time (HH:MM) if mentioned
3. Respond naturally in the same language as the customer (English or Nepali)
4. If unclear, ask clarifying questions and set intent to unknown
5. Confidence should be between 0.0 and 1.0
6. Keep response_message conversational and friendly
7. For Nepali responses, use Devanagari script

Respond ONLY with valid JSON:
{
    "intent": "confirm_appointment|cancel_appointment|reschedule_appointment|ask_question|transfer_to_human|unknown",
    "confidence": 0.0,
    "response_message": "Your conversational response here",
    "new_date": "",
    "new_time": ""
}
PROMPT;
    }

    protected function buildUserPrompt(array $conversationHistory, array $appointmentData): string
    {
        $history = '';
        foreach ($conversationHistory as $entry) {
            $speaker = $entry['speaker'] ?? 'unknown';
            $message = $entry['message'] ?? '';
            $history .= "$speaker: $message\n";
        }

        $appointmentInfo = '';
        if (! empty($appointmentData)) {
            $appointmentInfo = "Appointment: {$appointmentData['service']} on {$appointmentData['date']} at {$appointmentData['time']}";
        }

        return <<<PROMPT
$appointmentInfo

Conversation History:
$history

Analyze the customer's latest response and determine the intent.
Respond with the JSON format only.
PROMPT;
    }

    protected function defaultIntent(): array
    {
        return [
            'intent' => 'unknown',
            'confidence' => 0.0,
            'response_message' => 'I did not understand that. Could you please repeat?',
            'new_date' => '',
            'new_time' => '',
        ];
    }

    protected function mockResponse(string $prompt): string
    {
        if (stripos($prompt, 'confirm') !== false || stripos($prompt, 'yes') !== false) {
            return json_encode([
                'intent' => 'confirm_appointment',
                'confidence' => 0.95,
                'response_message' => 'Great! Your appointment has been confirmed. Thank you!',
                'new_date' => '',
                'new_time' => '',
            ]);
        }

        if (stripos($prompt, 'cancel') !== false || stripos($prompt, "won't be able") !== false) {
            return json_encode([
                'intent' => 'cancel_appointment',
                'confidence' => 0.9,
                'response_message' => 'I understand. Your appointment has been cancelled.',
                'new_date' => '',
                'new_time' => '',
            ]);
        }

        if (stripos($prompt, 'reschedule') !== false || stripos($prompt, 'monday') !== false || stripos($prompt, 'instead') !== false) {
            return json_encode([
                'intent' => 'reschedule_appointment',
                'confidence' => 0.85,
                'response_message' => 'I can help you reschedule. What date and time would you prefer?',
                'new_date' => '',
                'new_time' => '',
            ]);
        }

        if (stripos($prompt, 'speak') !== false || stripos($prompt, 'human') !== false || stripos($prompt, 'person') !== false) {
            return json_encode([
                'intent' => 'transfer_to_human',
                'confidence' => 0.95,
                'response_message' => 'I will transfer you to a human representative now.',
                'new_date' => '',
                'new_time' => '',
            ]);
        }

        return json_encode($this->defaultIntent());
    }
}
