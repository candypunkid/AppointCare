<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversationLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'call_log_id',
        'speaker',
        'message',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function callLog(): BelongsTo
    {
        return $this->belongsTo(CallLog::class);
    }
}
