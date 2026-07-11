<?php

namespace App\Models;

use App\Domains\Shared\Enums\Dispute\ReasonEnum;
use App\Domains\Shared\Enums\Dispute\StatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

// =====================
// CONFIGURATION
// =====================
#[Fillable([
    'booking_id',
    'initiator_id',
    'respondent_id',
    'dispute_reason',
    'title',
    'description',
    'evidence',
    'dispute_status',
    'assigned_to_id',
    'resolution_amount_credited',
    'resolution_notes',
    'resolved_at',
    'appeal_count',
    'last_appeal_at',
    'escalation_reason',
])]
class Dispute extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

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
            // State Machine & Reason Enums
            'dispute_reason' => ReasonEnum::class,
            'dispute_status' => StatusEnum::class,

            // JSONB Structured Evidence Log
            'evidence' => 'array',

            // Financial Adjustments
            'resolution_amount_credited' => 'decimal:2',

            // Counters
            'appeal_count' => 'integer',

            // Dates & Timestamps
            'resolved_at' => 'datetime',
            'last_appeal_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * The booking transaction linked directly to this conflict.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * The party who raised and submitted the dispute record.
     */
    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    /**
     * The party responding to the claims made in the dispute.
     */
    public function respondent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    /**
     * The platform support agent or specialist handling mediation.
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    /**
     * Determine if the dispute has reached its terminal resolved state.
     */
    protected function isClosed(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->dispute_status === StatusEnum::RESOLVED
        )->shouldCache();
    }

    /**
     * Evaluate whether this dispute case is high priority due to appeals.
     */
    protected function isHighPriority(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->appeal_count > 0 || $this->dispute_status === StatusEnum::ESCALATED
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Filter cases currently needing attention from the operations pool.
     */
    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to_id')
            ->whereIn('dispute_status', [StatusEnum::OPEN, StatusEnum::ESCALATED]);
    }

    /**
     * Filter cases by their underlying reason category.
     */
    public function scopeByReason($query, ReasonEnum $reason)
    {
        return $query->where('dispute_reason', $reason);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================

    /**
     * Allocate the conflict case to an operating support agent.
     */
    public function assignTo(User $agent): void
    {
        $this->update([
            'assigned_to_id' => $agent->id,
            'dispute_status' => StatusEnum::UNDER_REVIEW,
        ]);
    }

    /**
     * Finalize the case file with binding administrative adjustments.
     */
    public function closeCase(string $notes, ?float $creditAmount = null): void
    {
        $this->update([
            'dispute_status' => StatusEnum::RESOLVED,
            'resolution_notes' => $notes,
            'resolution_amount_credited' => $creditAmount,
            'resolved_at' => now(),
        ]);
    }
}
