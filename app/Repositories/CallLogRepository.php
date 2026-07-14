<?php

namespace App\Repositories;

use App\Models\CallLog;
use Illuminate\Pagination\LengthAwarePaginator;

class CallLogRepository
{
    public function findByTwilioSid(string $twilioCallSid): ?CallLog
    {
        return CallLog::with(['conversationLogs', 'appointment'])
            ->where('twilio_call_sid', $twilioCallSid)
            ->first();
    }

    public function findByAppointmentId(int $appointmentId): ?CallLog
    {
        return CallLog::with(['conversationLogs'])
            ->where('appointment_id', $appointmentId)
            ->latest()
            ->first();
    }

    public function getRecentByAppointment(int $appointmentId, int $limit = 5)
    {
        return CallLog::where('appointment_id', $appointmentId)
            ->latest()
            ->take($limit)
            ->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return CallLog::with('appointment')->latest()->paginate($perPage);
    }

    public function getFailedCalls(): LengthAwarePaginator
    {
        return CallLog::whereIn('status', ['failed', 'no-answer'])
            ->with('appointment')
            ->latest()
            ->paginate(15);
    }

    public function getCallStats(): array
    {
        return [
            'total' => CallLog::count(),
            'completed' => CallLog::where('status', 'completed')->count(),
            'failed' => CallLog::where('status', 'failed')->count(),
            'in_progress' => CallLog::where('status', 'in_progress')->count(),
            'avg_duration' => CallLog::whereNotNull('duration')->avg('duration'),
        ];
    }
}
