<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Support\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIConversation extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'appointment_request_id',
        'appointment_id',
        'customer_phone',
        'conversation_type',
        'twilio_call_sid',
        'status',
        'conversation_transcript',
        'action_taken',
        'started_at',
        'ended_at',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Get the tenant for this conversation.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the appointment request for this conversation.
     */
    public function appointmentRequest(): BelongsTo
    {
        return $this->belongsTo(AppointmentRequest::class);
    }

    /**
     * Get the appointment for this conversation.
     */
    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    /**
     * Scope to active tenant.
     */
    public function scopeForTenant($query, $tenant = null)
    {
        $tenant = $tenant ?? (app()->bound('tenant') ? app('tenant') : null) ?? auth()->user()?->tenant;
        if (is_numeric($tenant)) {
            return $query->where('tenant_id', (int) $tenant);
        }

        if (! $tenant) {
            return $query;
        }

        return $query->where('tenant_id', $tenant->id);
    }

    /**
     * Scope to completed conversations.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to failed conversations.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark conversation as in progress.
     */
    public function markAsInProgress(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark conversation as completed.
     */
    public function markAsCompleted(string $actionTaken, array $metadata = []): void
    {
        $this->update([
            'status' => 'completed',
            'action_taken' => $actionTaken,
            'ended_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    /**
     * Mark conversation as failed.
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'ended_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], ['error' => $reason]),
        ]);
    }
}
