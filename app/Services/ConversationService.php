<?php

namespace App\Services;

use App\Models\CallLog;
use App\Models\ConversationLog;
use Illuminate\Support\Facades\Log;

class ConversationService
{
    public function __construct(
        protected OpenAIService $openAIService,
        protected AppointmentService $appointmentService
    ) {}

    public function startCallLog(int $appointmentId, string $twilioCallSid): CallLog
    {
        return CallLog::create([
            'appointment_id' => $appointmentId,
            'twilio_call_sid' => $twilioCallSid,
            'status' => 'in_progress',
        ]);
    }

    public function appendMessage(CallLog $callLog, string $speaker, string $message): ConversationLog
    {
        return ConversationLog::create([
            'call_log_id' => $callLog->id,
            'speaker' => $speaker,
            'message' => $message,
            'created_at' => now(),
        ]);
    }

    public function processConversation(CallLog $callLog, array $appointmentData): array
    {
        $history = $callLog->conversationLogs()
            ->orderBy('created_at')
            ->get()
            ->map(fn ($log) => ['speaker' => $log->speaker, 'message' => $log->message])
            ->toArray();

        $result = $this->openAIService->analyzeIntent($history, $appointmentData);

        $callLog->update([
            'detected_intent' => $result['intent'],
            'ai_response' => $result['response_message'],
            'transcript' => collect($history)->map(fn ($h) => "{$h['speaker']}: {$h['message']}")->implode("\n"),
        ]);

        $this->appendMessage($callLog, 'ai', $result['response_message']);

        $this->handleIntent($callLog, $result);

        return $result;
    }

    protected function handleIntent(CallLog $callLog, array $result): void
    {
        $intent = $result['intent'];
        $appointment = $callLog->appointment;

        if (! $appointment) {
            return;
        }

        try {
            match ($intent) {
                'confirm_appointment' => $this->appointmentService->confirmAppointment($appointment, $callLog, $result['confidence']),
                'cancel_appointment' => $this->appointmentService->cancelAppointment($appointment, $callLog, $result['confidence']),
                'reschedule_appointment' => $this->appointmentService->rescheduleAppointment($appointment, $callLog, $result['confidence'], $result['new_date'], $result['new_time']),
                'transfer_to_human' => $this->appointmentService->transferToHuman($appointment, $callLog),
                default => Log::info('Unknown intent, no action taken', ['call_log_id' => $callLog->id]),
            };
        } catch (\Exception $e) {
            Log::error('Failed to handle intent', [
                'intent' => $intent,
                'call_log_id' => $callLog->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function completeCall(CallLog $callLog, string $status = 'completed'): void
    {
        $callLog->update(['status' => $status]);

        if ($callLog->appointment) {
            $appointment = $callLog->appointment;

            if (! in_array($appointment->status, ['confirmed', 'cancelled', 'rescheduled', 'transferred'])) {
                $appointment->update(['status' => 'completed']);
            }
        }
    }

    public function failCall(CallLog $callLog, string $reason = ''): void
    {
        $callLog->update([
            'status' => 'failed',
            'ai_response' => ($callLog->ai_response ? $callLog->ai_response."\n" : '')."FAILED: $reason",
        ]);
    }
}
