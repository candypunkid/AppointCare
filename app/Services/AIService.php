<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class AIService
{
    protected string $url;
    protected string $model;

    public function __construct()
    {
        $this->url = env('OLLAMA_URL', 'http://127.0.0.1:11434');
        $this->model = env('OLLAMA_MODEL', 'gemma3');
    }

    public function isAvailable(): bool
    {
        try {
            return Http::timeout(5)
                // ->get($this->url)
                ->get($this->url . '/api/tags')
                ->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function chat(string $prompt, array $options = []): string
    {
        if (! $this->isAvailable()) {
            throw new Exception('Ollama is not running.');
        }

        $system = $options['system']
            ?? 'You are a helpful assistant for AppointCare. Provide short, actionable answers.';

        $response = Http::timeout(120)
            ->post($this->url . '/api/chat', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $system,
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                'stream' => false,
            ]);

        if (! $response->successful()) {
            throw new Exception('Failed to communicate with Ollama.');
        }

        return $response->json()['message']['content'] ?? '';
    }
}
