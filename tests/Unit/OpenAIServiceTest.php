<?php

namespace Tests\Unit;

use App\Services\OpenAIService;
use Tests\TestCase;

class OpenAIServiceTest extends TestCase
{
    protected OpenAIService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(OpenAIService::class);
    }

    public function test_it_can_be_instantiated(): void
    {
        $this->assertInstanceOf(OpenAIService::class, $this->service);
    }

    public function test_it_returns_default_intent_for_empty_conversation(): void
    {
        $result = $this->service->analyzeIntent([], []);

        $this->assertArrayHasKey('intent', $result);
        $this->assertArrayHasKey('confidence', $result);
        $this->assertArrayHasKey('response_message', $result);
        $this->assertArrayHasKey('new_date', $result);
        $this->assertArrayHasKey('new_time', $result);
    }

    public function test_it_returns_unknown_intent_with_low_confidence_by_default(): void
    {
        $result = $this->service->analyzeIntent([
            ['speaker' => 'ai', 'message' => 'Hello, can you confirm your appointment?'],
            ['speaker' => 'customer', 'message' => 'Maybe later'],
        ], []);

        $this->assertEquals('unknown', $result['intent']);
        $this->assertLessThanOrEqual(0.5, $result['confidence']);
    }

    public function test_it_is_available_in_mock_mode(): void
    {
        $this->assertTrue($this->service->isAvailable());
    }

    public function test_mock_responds_with_json(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mockResponse');
        $method->setAccessible(true);

        $confirmResponse = $method->invoke($this->service, 'Yes, I confirm');
        $data = json_decode($confirmResponse, true);
        $this->assertEquals('confirm_appointment', $data['intent']);

        $cancelResponse = $method->invoke($this->service, "I won't be able to come");
        $data = json_decode($cancelResponse, true);
        $this->assertEquals('cancel_appointment', $data['intent']);

        $transferResponse = $method->invoke($this->service, 'I want to speak with someone');
        $data = json_decode($transferResponse, true);
        $this->assertEquals('transfer_to_human', $data['intent']);
    }
}
