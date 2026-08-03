<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingHowStep extends Model
{
    protected $fillable = [
        'tenant_id',
        'step_number',
        'title',
        'description',
        'sort_order',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
