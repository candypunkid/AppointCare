<?php

namespace Tests\Unit;

use App\Services\TwilioService;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TwilioServiceTest extends TestCase
{
    public function test_webhook_urls_are_built_from_config_app_url_not_request_host(): void
    {
        config()->set('app.url', 'https://employed-deafness-familiar.ngrok-free.dev');

        URL::forceRootUrl('http://localhost:8000');

        $service = new TwilioService;

        $gather = $service->createGatherUrlForAppointment(5);
        $status = $service->createStatusUrlForAppointment(5);

        $this->assertSame(
            'https://employed-deafness-familiar.ngrok-free.dev/api/twilio/voice?appointment_id=5',
            $gather
        );
        $this->assertSame(
            'https://employed-deafness-familiar.ngrok-free.dev/api/twilio/status?appointment_id=5',
            $status
        );
    }
}