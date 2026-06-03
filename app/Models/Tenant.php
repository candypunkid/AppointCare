<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'phone',
        'email',
        'description',
        'logo_path',
        'settings',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($tenant) {
            if (empty($tenant->slug) && ! empty($tenant->name)) {
                $tenant->slug = static::generateUniqueSlug($tenant->name);
            }
        });

        static::updating(function ($tenant) {
            // regenerate slug if name changed
            if ($tenant->isDirty('name') && ! empty($tenant->name)) {
                $tenant->slug = static::generateUniqueSlug($tenant->name, $tenant->id);
            }
            // ensure slug exists
            if (empty($tenant->slug) && ! empty($tenant->name)) {
                $tenant->slug = static::generateUniqueSlug($tenant->name, $tenant->id);
            }
        });
    }

    protected static function generateUniqueSlug(string $name, $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    protected $casts = [
        'settings' => 'json',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users for this tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the appointments for this tenant.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get the appointment requests for this tenant.
     */
    public function appointmentRequests(): HasMany
    {
        return $this->hasMany(AppointmentRequest::class);
    }

    /**
     * Get the AI conversations for this tenant.
     */
    public function aiConversations(): HasMany
    {
        return $this->hasMany(AIConversation::class);
    }

    /**
     * Get the admin users for this tenant.
     */
    public function adminUsers(): HasMany
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * Get the staff users for this tenant.
     */
    public function staffUsers(): HasMany
    {
        return $this->users()->where('role', 'staff');
    }

    /**
     * Get the customers for this tenant.
     */
    public function customers(): HasMany
    {
        return $this->users()->where('role', 'customer');
    }
}
