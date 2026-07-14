<?php

namespace App\Repositories;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AppointmentRepository
{
    public function findById(int $id): ?Appointment
    {
        return Appointment::with(['customer', 'staff', 'callLogs'])->find($id);
    }

    public function findWithCallLogs(int $id): ?Appointment
    {
        return Appointment::with(['callLogs.conversationLogs', 'aiActions'])->find($id);
    }

    public function getUpcomingReminders(int $hoursAhead = 24): Collection
    {
        $target = now()->addHours($hoursAhead);

        return Appointment::with('customer')
            ->whereDate('scheduled_at', $target->toDateString())
            ->whereTime('scheduled_at', '>=', $target->copy()->subHour()->format('H:i:s'))
            ->whereTime('scheduled_at', '<=', $target->copy()->addHour()->format('H:i:s'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('customer', fn ($q) => $q->whereNotNull('phone'))
            ->get();
    }

    public function getAppointmentsDueForReminder(string $frequency = '24h'): Collection
    {
        $query = Appointment::with('customer')
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('customer', fn ($q) => $q->whereNotNull('phone'));

        if ($frequency === '24h') {
            $target = now()->addDay();
            $query->whereDate('scheduled_at', $target->toDateString())
                ->whereTime('scheduled_at', '>=', $target->copy()->subHour()->format('H:i:s'))
                ->whereTime('scheduled_at', '<=', $target->copy()->addHour()->format('H:i:s'));
        } elseif ($frequency === '2h') {
            $target = now()->addHours(2);
            $query->whereDate('scheduled_at', $target->toDateString())
                ->whereTime('scheduled_at', '>=', $target->copy()->subMinutes(30)->format('H:i:s'))
                ->whereTime('scheduled_at', '<=', $target->copy()->addMinutes(30)->format('H:i:s'));
        }

        return $query->get();
    }

    public function getAnalytics(): array
    {
        $totalCalls = Appointment::count();
        $confirmed = Appointment::where('status', 'confirmed')->count();
        $cancelled = Appointment::where('status', 'cancelled')->count();
        $rescheduled = Appointment::where('status', 'rescheduled')->count();
        $noShows = Appointment::where('status', 'completed')
            ->where('notes', 'like', '%no-show%')
            ->count();

        $avgDuration = \App\Models\CallLog::whereNotNull('duration')
            ->avg('duration');

        $aiActions = \App\Models\AiAction::count();
        $successfulActions = \App\Models\AiAction::where('confidence', '>=', 0.7)->count();
        $aiAccuracy = $aiActions > 0 ? round(($successfulActions / $aiActions) * 100, 2) : 0;

        return [
            'total_calls' => $totalCalls,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
            'rescheduled' => $rescheduled,
            'no_shows' => $noShows,
            'average_call_duration_seconds' => round($avgDuration ?? 0),
            'ai_accuracy_percent' => $aiAccuracy,
        ];
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Appointment::with(['customer', 'staff'])->latest()->paginate($perPage);
    }
}
