<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LandingTestimonial extends Model
{
    protected $fillable = [
        'tenant_id',
        'rating',
        'text',
        'author_name',
        'author_role',
        'sort_order',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
