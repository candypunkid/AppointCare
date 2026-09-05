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
        $this->mock = (bool) env('AI_MOCK', false) || empty($apiKey);

        if (! $this->mock) {
            $this->client = \OpenAI::client($apiKey);
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

        $attempts = 0;
        while (true) {
            try {
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
                break;
            } catch (Exception $e) {
                $isRateLimited = str_contains($e->getMessage(), 'rate limit');

                if ($isRateLimited && $attempts < 3) {
                    $attempts++;
                    Log::warning('OpenAI rate limited, retrying', [
                        'attempt' => $attempts,
                        'error' => $e->getMessage(),
                    ]);
                    sleep($attempts);

                    continue;
                }

                throw $e;
            }
        }

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
        return <<<'PROMPT'
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
        $customerLines = [];
        preg_match_all('/customer:\s*(.+)$/m', $prompt, $matches);
        $customerLines = array_map('trim', $matches[1] ?? []);
        $text = strtolower(end($customerLines) ?: $prompt);

        $rescheduleWords = ['reschedule', 'rescheduled', 'postpone', 'postponed', 'post my', 'instead', 'another day', 'another time', 'another slot', 'any other', 'change the', 'change my', 'next week', 'next month', 'on monday', 'on tuesday', 'on wednesday', 'on thursday', 'on friday', 'on saturday', 'on sunday'];
        foreach ($rescheduleWords as $word) {
            if (str_contains($text, $word)) {
                [$newDate, $newTime] = $this->extractRescheduleDateTime($text);

                $responseMessage = $newDate
                    ? 'I have rescheduled your appointment to '.($newDate.($newTime ? ' at '.$newTime : '')).'. Thank you!'
                    : 'I can help you reschedule. What date and time would you prefer?';

                return json_encode([
                    'intent' => 'reschedule_appointment',
                    'confidence' => 0.85,
                    'response_message' => $responseMessage,
                    'new_date' => $newDate ?? '',
                    'new_time' => $newTime ?? '',
                ]);
            }
        }

        $cancelWords = ['cancel', 'cancelled', "won't be able", "won't come", "won't attend", 'not be able', 'not able', 'cannot', "can't", "can not", 'unable', 'not coming', "can't make it", 'not going to', 'won one'];
        if (preg_match('/\bno\b/', str_replace(['no,', 'no '], [' no ', ' no '], $text)) || str_starts_with($text, 'no ')) {
            $cancelWords[] = 'xno';
        }
        foreach ($cancelWords as $word) {
            if (str_contains($text, $word)) {
                return json_encode([
                    'intent' => 'cancel_appointment',
                    'confidence' => 0.9,
                    'response_message' => 'I understand. Your appointment has been cancelled.',
                    'new_date' => '',
                    'new_time' => '',
                ]);
            }
        }

        if (preg_match('/\byes\b/', $text) || str_contains($text, 'confirm') || str_contains($text, 'will attend') || str_contains($text, 'attend') || str_contains($text, "i'll be") || str_contains($text, 'will be there') || str_contains($text, 'be there')) {
            return json_encode([
                'intent' => 'confirm_appointment',
                'confidence' => 0.95,
                'response_message' => 'Great! Your appointment has been confirmed. Thank you!',
                'new_date' => '',
                'new_time' => '',
            ]);
        }

        if (str_contains($text, 'speak') || str_contains($text, 'human') || str_contains($text, 'person') || str_contains($text, 'representative') || str_contains($text, 'receptionist') || str_contains($text, 'agent')) {
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

    protected function extractRescheduleDateTime(string $text): array
    {
        $newDate = null;
        $newTime = null;

        if (preg_match('/\b(\d{1,2})(?:[:.](\d{2}))?\s*(am|pm)\b/i', $text, $m)) {
            $hour = (int) $m[1];
            $minute = isset($m[2]) ? (int) $m[2] : 0;
            $ampm = strtolower($m[3]);

            if ($ampm[0] === 'p' && $hour < 12) {
                $hour += 12;
            }
            if ($ampm[0] === 'a' && $hour === 12) {
                $hour = 0;
            }

            $newTime = sprintf('%02d:%02d', $hour, $minute);
        } elseif (preg_match('/\b(\d{1,2}):(\d{2})\b/', $text, $m)) {
            $newTime = sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
        }

        if (str_contains($text, 'tomorrow')) {
            $newDate = now()->addDay()->format('Y-m-d');
        } elseif (str_contains($text, 'next week')) {
            $newDate = now()->addWeek()->format('Y-m-d');
        } elseif (preg_match('/\b(?:on |this |next )?(monday|tuesday|wednesday|thursday|friday|saturday|sunday)\b/', $text, $m)) {
            $days = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0];
            $target = $days[strtolower($m[1])];
            $today = (int) now()->dayOfWeek;
            $diff = ($target - $today + 7) % 7;

            if ($diff === 0) {
                $diff = 7;
            }

            $newDate = now()->addDays($diff)->format('Y-m-d');
        }

        return [$newDate, $newTime];
    }
}
