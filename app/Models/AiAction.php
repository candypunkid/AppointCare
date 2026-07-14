<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAction extends Model
{
    protected $fillable = [
        'appointment_id',
        'action',
        'old_value',
        'new_value',
        'confidence',
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
