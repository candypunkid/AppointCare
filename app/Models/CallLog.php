<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CallLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'twilio_call_sid',
        'transcript',
        'detected_intent',
        'ai_response',
        'recording_url',
        'duration',
        'status',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function conversationLogs(): HasMany
    {
        return $this->hasMany(ConversationLog::class);
    }
}
