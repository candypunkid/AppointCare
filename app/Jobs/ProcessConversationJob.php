<?php

namespace App\Jobs;

use App\Models\CallLog;
use App\Services\ConversationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessConversationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;
    public int $tries = 2;

    public function __construct(
        public CallLog $callLog
    ) {
        $this->queue = 'conversations';
    }

    public function handle(ConversationService $conversationService): void
    {
        if ($this->callLog->status !== 'in_progress') {
            Log::info('Skipping conversation processing: call not in progress', [
                'call_log_id' => $this->callLog->id,
                'status' => $this->callLog->status,
            ]);
            return;
        }

        try {
            $appointment = $this->callLog->appointment;

            if (! $appointment) {
                Log::warning('Cannot process conversation: no associated appointment', [
                    'call_log_id' => $this->callLog->id,
                ]);
                return;
            }

            $appointmentData = [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer?->name ?? 'Customer',
                'service' => $appointment->service ?? 'Appointment',
                'date' => $appointment->scheduled_at?->format('Y-m-d') ?? '',
                'time' => $appointment->scheduled_at?->format('g:i A') ?? '',
            ];

            $result = $conversationService->processConversation($this->callLog, $appointmentData);

            Log::info('Conversation processed', [
                'call_log_id' => $this->callLog->id,
                'intent' => $result['intent'],
                'confidence' => $result['confidence'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to process conversation', [
                'call_log_id' => $this->callLog->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Exception $exception): void
    {
        Log::error('ProcessConversationJob failed permanently', [
            'call_log_id' => $this->callLog->id,
            'error' => $exception->getMessage(),
        ]);

        try {
            $this->callLog->update(['status' => 'failed']);
        } catch (\Exception $e) {
            Log::error('Failed to update call log status after job failure', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
