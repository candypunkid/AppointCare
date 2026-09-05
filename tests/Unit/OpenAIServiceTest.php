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

        $confirmResponse = $method->invoke($this->service, "customer: Yes, I confirm\n");
        $data = json_decode($confirmResponse, true);
        $this->assertEquals('confirm_appointment', $data['intent']);

        $cancelResponse = $method->invoke($this->service, "customer: I won't be able to come\n");
        $data = json_decode($cancelResponse, true);
        $this->assertEquals('cancel_appointment', $data['intent']);

        $transferResponse = $method->invoke($this->service, "customer: I want to speak with someone\n");
        $data = json_decode($transferResponse, true);
        $this->assertEquals('transfer_to_human', $data['intent']);
    }

    public function test_mock_recognizes_common_cancel_phrasing(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mockResponse');
        $method->setAccessible(true);

        foreach (["No, I am not able to come to tomorrow appointment. Sorry.", 'So I will not be able to come for tomorrow.'] as $utterance) {
            $data = json_decode($method->invoke($this->service, "customer: $utterance\n"), true);
            $this->assertEquals('cancel_appointment', $data['intent'], "phrasing: $utterance");
        }
    }

    public function test_mock_recognizes_reschedule_phrasing(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mockResponse');
        $method->setAccessible(true);

        $data = json_decode($method->invoke($this->service, "customer: Please postpone to next week\n"), true);
        $this->assertEquals('reschedule_appointment', $data['intent']);
        $this->assertNotEmpty($data['new_date'], 'should extract a new date from the speech');
    }

    public function test_mock_extracts_time_when_customer_gives_one(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mockResponse');
        $method->setAccessible(true);

        $data = json_decode($method->invoke($this->service, "customer: Please reschedule to friday at 3pm\n"), true);
        $this->assertEquals('reschedule_appointment', $data['intent']);
        $this->assertNotEmpty($data['new_date']);
        $this->assertSame('15:00', $data['new_time']);
    }

    public function test_mock_reschedule_without_date_keeps_asking(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mockResponse');
        $method->setAccessible(true);

        $data = json_decode($method->invoke($this->service, "customer: Please postpone\n"), true);
        $this->assertEquals('reschedule_appointment', $data['intent']);
        $this->assertSame('', $data['new_date']);
        $this->assertSame('', $data['new_time']);
        $this->assertStringContainsString('date and time', $data['response_message']);
    }

    public function test_mock_does_not_confirm_on_yesterday(): void
    {
        $reflection = new \ReflectionClass($this->service);
        $method = $reflection->getMethod('mockResponse');
        $method->setAccessible(true);

        $data = json_decode($method->invoke($this->service, "customer: Yesterday I was not well\n"), true);
        $this->assertNotEquals('confirm_appointment', $data['intent']);
        $this->assertEquals('unknown', $data['intent']);
    }
}
