<?php

namespace Modules\Appointment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Appointment\Events\AppointmentStatusChanged;

class Appointment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'appointment_date',
        'description',
        'status', // pending, calling, confirmed, cancelled, rescheduled, no_answer, failed
        'twilio_call_sid',
        'call_transcript',
        'ai_summary',
        'appointment_type',
        'duration_minutes',
        'call_attempts',
        'last_called_at',
        'tenant_id',
        'notes',
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'last_called_at' => 'datetime',
    ];

    protected $dispatchesEvents = [
        'updated' => AppointmentStatusChanged::class,
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['confirmed', 'cancelled', 'rescheduled']);
    }

    public function markAsConfirmed(): void
    {
        $this->update(['status' => 'confirmed']);
    }

    public function markAsCompleted(string $transcript = null, string $summary = null): void
    {
        $this->update([
            'status' => 'completed',
            'call_transcript' => $transcript,
            'ai_summary' => $summary,
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    public function confirm(string $notes = null): void
    {
        $this->update(['status' => 'confirmed', 'notes' => $notes]);
    }

    public function cancel(string $reason = null): void
    {
        $this->update(['status' => 'cancelled', 'notes' => $reason]);
    }

    public function reschedule(string $newDateTime, string $notes = null): void
    {
        $this->update([
            'status' => 'rescheduled',
            'appointment_date' => $newDateTime,
            'notes' => $notes
        ]);
    }
}
