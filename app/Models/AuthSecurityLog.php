<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthSecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event_type',
        'channel',
        'identifier_hash',
        'ip_address',
        'user_agent',
        'attempt_number',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'attempt_number' => 'integer',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}