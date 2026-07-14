<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\ConversationService;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MakeReminderCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Appointment $appointment
    ) {
        $this->queue = 'calls';
    }

    public function handle(TwilioService $twilioService, ConversationService $conversationService): void
    {
        $customer = $this->appointment->customer;

        if (! $customer?->phone) {
            Log::warning('Cannot make reminder call: customer has no phone', [
                'appointment_id' => $this->appointment->id,
            ]);
            return;
        }

        if (! in_array($this->appointment->status, ['pending', 'confirmed'])) {
            Log::info('Skipping reminder call: appointment not in callable status', [
                'appointment_id' => $this->appointment->id,
                'status' => $this->appointment->status,
            ]);
            return;
        }

        try {
            $voiceUrl = route('api.twilio.voice', ['appointment_id' => $this->appointment->id]);
            $statusUrl = route('api.twilio.status', ['appointment_id' => $this->appointment->id]);

            $result = $twilioService->initiateVoiceCall(
                $customer->phone,
                $voiceUrl,
                $statusUrl,
                $this->appointment->id
            );

            if ($result['success']) {
                $callLog = $conversationService->startCallLog(
                    $this->appointment->id,
                    $result['call_sid']
                );

                $conversationService->appendMessage(
                    $callLog,
                    'ai',
                    "Initiating reminder call to {$customer->name} for appointment on {$this->appointment->scheduled_at?->format('M d, Y \\a\\t g:i A')}"
                );

                $this->appointment->update([
                    'status' => 'in_progress',
                    'metadata' => array_merge($this->appointment->metadata ?? [], [
                        'last_reminder_call_at' => now()->toIso8601String(),
                        'last_call_sid' => $result['call_sid'],
                    ]),
                ]);

                Log::info('Reminder call initiated successfully', [
                    'appointment_id' => $this->appointment->id,
                    'call_sid' => $result['call_sid'],
                ]);
            } else {
                Log::error('Failed to initiate reminder call', [
                    'appointment_id' => $this->appointment->id,
                    'error' => $result['error'] ?? 'unknown',
                ]);

                $this->release(120);
            }
        } catch (\Exception $e) {
            Log::error('Exception in reminder call job', [
                'appointment_id' => $this->appointment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error('MakeReminderCallJob failed permanently', [
            'appointment_id' => $this->appointment->id,
            'error' => $exception->getMessage(),
        ]);

        $this->appointment->update([
            'status' => 'pending',
            'notes' => ($this->appointment->notes ?? '') . "\nReminder call failed: " . $exception->getMessage(),
        ]);
    }
}
