<?php

namespace App\Models;

use App\Support\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentRequest extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'service',
        'preferred_at',
        'message',
        'status',
    ];

    protected $casts = [
        'preferred_at' => 'datetime',
    ];

    /**
     * Get the tenant for this request.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the AI conversations for this request.
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AIConversation::class);
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
     * Scope to new requests.
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope to contacted requests.
     */
    public function scopeContacted($query)
    {
        return $query->where('status', 'contacted');
    }

    /**
     * Mark this request as contacted.
     */
    public function markAsContacted(): void
    {
        $this->update(['status' => 'contacted']);
    }

    /**
     * Mark this request as scheduled.
     */
    public function markAsScheduled(): void
    {
        $this->update(['status' => 'scheduled']);
    }
}
