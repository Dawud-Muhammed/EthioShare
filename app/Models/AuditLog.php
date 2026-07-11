<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

// --* Disable standard timestamps since audit logs are immutable.
#[WithoutTimestamps]

#[Fillable([
    'user_id',
    'action_type',
    'entity_type',
    'entity_id',
    'old_values',
    'new_values',
    'ip_address',
    'user_agent',
    'request_id',
    'created_at',
])]
#[Hidden([
    'ip_address',
    'user_agent',
    'request_id',
])]
class AuditLog extends Model
{
    use HasFactory, HasUlids;

    // =====================
    // CASTING PIPELINE
    // =====================

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // JSONB Payloads for delta tracking
            'old_values' => 'array',
            'new_values' => 'array',

            // Timestamps
            'created_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * The user who performed the action, if applicable.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The polymorphic entity (e.g., Asset, Booking, User) that was altered.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'entity_type', 'entity_id');
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Scope to find logs related to a specific HTTP request lifecycle.
     */
    public function scopeByRequest($query, string $requestId)
    {
        return $query->where('request_id', $requestId);
    }
}
