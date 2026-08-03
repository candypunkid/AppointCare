<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingPlan extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'monthly_price',
        'yearly_price',
        'is_popular',
        'features',
        'button_text',
        'badge_text',
        'sort_order',
    ];

    protected $casts = [
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
        'is_popular' => 'boolean',
        'features' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
