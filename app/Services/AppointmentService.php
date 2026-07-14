<?php

namespace App\Services;

use App\Models\AiAction;
use App\Models\Appointment;
use App\Models\CallLog;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentRescheduled;
use Illuminate\Support\Facades\Log;

class AppointmentService
{
    public function __construct(
        protected TwilioService $twilioService
    ) {}

    public function confirmAppointment(Appointment $appointment, CallLog $callLog, float $confidence): void
    {
        $oldStatus = $appointment->status;

        $appointment->update(['status' => 'confirmed']);

        $this->logAiAction($appointment, 'confirm', $oldStatus, 'confirmed', $confidence);

        event(new AppointmentConfirmed($appointment, $callLog));

        Log::info('Appointment confirmed via AI', [
            'appointment_id' => $appointment->id,
            'confidence' => $confidence,
        ]);
    }

    public function cancelAppointment(Appointment $appointment, CallLog $callLog, float $confidence, ?string $reason = null): void
    {
        $oldStatus = $appointment->status;
        $oldDate = $appointment->scheduled_at?->toDateTimeString();

        $appointment->update([
            'status' => 'cancelled',
            'notes' => $reason ? "Cancelled by AI: $reason" : 'Cancelled via AI call',
        ]);

        $this->logAiAction($appointment, 'cancel', $oldDate, 'cancelled', $confidence);

        event(new AppointmentCancelled($appointment, $callLog, $reason));

        Log::info('Appointment cancelled via AI', [
            'appointment_id' => $appointment->id,
            'confidence' => $confidence,
        ]);
    }

    public function rescheduleAppointment(Appointment $appointment, CallLog $callLog, float $confidence, string $newDate = '', string $newTime = ''): void
    {
        $oldDate = $appointment->scheduled_at?->toDateTimeString();
        $oldStatus = $appointment->status;

        if ($newDate && $newTime) {
            try {
                $newDateTime = \Carbon\Carbon::parse("$newDate $newTime");
            } catch (\Exception $e) {
                $newDateTime = now()->addDay();
            }
        } elseif ($newDate) {
            try {
                $existingTime = $appointment->scheduled_at?->format('H:i') ?? '09:00';
                $newDateTime = \Carbon\Carbon::parse("$newDate $existingTime");
            } catch (\Exception $e) {
                $newDateTime = now()->addDay();
            }
        } else {
            $newDateTime = now()->addDay();
        }

        $appointment->update([
            'status' => 'rescheduled',
            'scheduled_at' => $newDateTime,
            'metadata' => array_merge($appointment->metadata ?? [], [
                'previous_scheduled_at' => $oldDate,
                'rescheduled_by' => 'ai',
            ]),
        ]);

        $this->logAiAction($appointment, 'reschedule', $oldDate, $newDateTime->toDateTimeString(), $confidence);

        event(new AppointmentRescheduled($appointment, $callLog, $oldDate));

        Log::info('Appointment rescheduled via AI', [
            'appointment_id' => $appointment->id,
            'old_date' => $oldDate,
            'new_date' => $newDateTime->toDateTimeString(),
            'confidence' => $confidence,
        ]);
    }

    public function transferToHuman(Appointment $appointment, CallLog $callLog): void
    {
        $appointment->update(['status' => 'in_progress']);

        $this->logAiAction($appointment, 'transfer_to_human', $appointment->status, 'in_progress', 1.0);

        Log::info('Call transferred to human', ['appointment_id' => $appointment->id]);
    }

    public function checkSlotAvailability(string $date, ?string $time = null): array
    {
        $query = Appointment::whereDate('scheduled_at', $date)
            ->whereIn('status', ['pending', 'confirmed']);

        if ($time) {
            $query->whereTime('scheduled_at', $time);
        }

        $existingCount = $query->count();

        $maxSlots = config('appointcare.max_slots_per_slot', 1);

        return [
            'available' => $existingCount < $maxSlots,
            'existing_count' => $existingCount,
            'max_slots' => $maxSlots,
        ];
    }

    public function findAvailableSlots(string $date, string $service = ''): array
    {
        $startHour = config('appointcare.business_hours_start', 9);
        $endHour = config('appointcare.business_hours_end', 17);
        $slotDuration = config('appointcare.slot_duration_minutes', 60);

        $bookedSlots = Appointment::whereDate('scheduled_at', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('scheduled_at')
            ->map(fn ($dt) => $dt->format('H:i'))
            ->toArray();

        $available = [];
        $current = \Carbon\Carbon::parse($date)->setHour($startHour)->setMinute(0);

        $end = \Carbon\Carbon::parse($date)->setHour($endHour)->setMinute(0);

        while ($current->lessThan($end)) {
            $timeStr = $current->format('H:i');
            if (! in_array($timeStr, $bookedSlots)) {
                $available[] = $timeStr;
            }
            $current->addMinutes($slotDuration);
        }

        return $available;
    }

    protected function logAiAction(Appointment $appointment, string $action, mixed $oldValue, mixed $newValue, float $confidence): void
    {
        AiAction::create([
            'appointment_id' => $appointment->id,
            'action' => $action,
            'old_value' => (string) ($oldValue ?? ''),
            'new_value' => (string) ($newValue ?? ''),
            'confidence' => $confidence,
        ]);
    }
}
