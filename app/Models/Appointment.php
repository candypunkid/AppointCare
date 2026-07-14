<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Support\Traits\BelongsToTenant;

class Appointment extends Model
{
    use SoftDeletes, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'customer_id',
        'staff_id',
        'service',
        'scheduled_at',
        'scheduled_end_at',
        'status',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'scheduled_end_at' => 'datetime',
        'metadata' => 'json',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function aiConversations(): HasMany
    {
        return $this->hasMany(AIConversation::class);
    }

    public function callLogs(): HasMany
    {
        return $this->hasMany(CallLog::class);
    }

    public function aiActions(): HasMany
    {
        return $this->hasMany(AiAction::class);
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
     * Scope to specific customer.
     */
    public function scopeForCustomer($query, User $customer)
    {
        return $query->where('customer_id', $customer->id);
    }

    /**
     * Scope to upcoming appointments.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Scope to past appointments.
     */
    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<', now());
    }
}
