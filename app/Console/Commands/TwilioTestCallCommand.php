<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\TwilioService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TwilioTestCallCommand extends Command
{
    protected $signature = 'twilio:test-call {phone? : E.164 phone number to call (defaults to the appointment customer or APPOINTCARE_ADMIN_PHONE)} {--appointment= : Appointment ID to attach conversation context so the AI can confirm/cancel/reschedule it}';

    protected $description = 'Place a test outbound AI call via Twilio (webhooks must be publicly reachable, e.g. via ngrok).';

    public function handle(TwilioService $twilioService): int
    {
        $appointment = null;

        if ($this->option('appointment')) {
            $appointment = Appointment::with('customer')->find($this->option('appointment'));
            if (! $appointment) {
                $this->error("Appointment {$this->option('appointment')} not found.");

                return self::FAILURE;
            }
        }

        $phone = $this->argument('phone') ?: $appointment?->customer?->phone ?: config('appointcare.admin_phone');

        if (! $phone) {
            $this->error('No phone number provided. Pass one, use --appointment, or set APPOINTCARE_ADMIN_PHONE in .env.');

            return self::FAILURE;
        }

        $voiceUrl = route('api.twilio.voice');
        $statusUrl = route('api.twilio.status');

        if ($appointment) {
            $voiceUrl .= '?appointment_id='.$appointment->id;
            $statusUrl .= '?appointment_id='.$appointment->id;
        }

        $this->info("Placing test call to {$phone}...");
        $this->line('Appointment: '.($appointment ? "#{$appointment->id} ({$appointment->service})" : 'none'));
        $this->line("Voice URL: {$voiceUrl}");
        $this->line("Status URL: {$statusUrl}");

        $result = $twilioService->initiateVoiceCall($phone, $voiceUrl, $statusUrl);

        if (! $result['success']) {
            $this->error('Call failed: '.($result['error'] ?? 'unknown error'));

            return self::FAILURE;
        }

        $this->info('Call initiated. Call SID: '.$result['call_sid']);

        Log::info('Manual test call placed', [
            'phone' => $phone,
            'call_sid' => $result['call_sid'],
        ]);

        return self::SUCCESS;
    }
}
