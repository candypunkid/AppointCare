<?php

namespace App\Services;

use Exception;

class AIService
{
    protected $client;

    protected string $model;

    protected bool $mock = false;

    public function __construct()
    {
        $apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');

        if ($apiKey) {
            // Use the helper from the vendor package to create a client
            $this->client = \OpenAI::client($apiKey);
        } else {
            // allow a demo/mock mode so the frontpage assistant works without a key
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

        // Mock/demo response when SDK or key not available
        if ($this->mock && empty($this->client)) {
            // simple canned reply for demo purposes
            $short = strlen($prompt) > 200 ? substr($prompt, 0, 197).'...' : $prompt;

            return "(Demo) I received your question: \"{$short}\".\n\nThis is a mock response because no OpenAI API key is configured. Set OPENAI_API_KEY in your .env to enable real responses.";
        }

        $system = $options['system'] ?? 'You are a helpful assistant for AppointCare. Provide short, actionable answers.';

        $response = $this->client->chat()->create([
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $options['max_tokens'] ?? 400,
        ]);

        $choice = $response->choices[0] ?? null;
        if (! $choice) {
            return '';
        }

        return $choice->message->content ?? '';
    }
}
