<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'reviewable_type',
    'reviewable_id',
    'reviewer_id',
    'booking_id',
    'rating',
    'title',
    'comment',
    'cleanliness_rating',
    'punctuality_rating',
    'communication_rating',
    'condition_upon_return_rating',
    'value_for_money_rating',
    'response_from_reviewee_id',
    'response_text',
    'responded_at',
    'is_verified_booking',
    'is_flagged',
    'flag_reason',
])]
#[Hidden([
    'flag_reason',
    'deleted_at',
])]
class Review extends Model
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
            // Strict Integers for Database Constraints
            'rating' => 'integer',
            'cleanliness_rating' => 'integer',
            'punctuality_rating' => 'integer',
            'communication_rating' => 'integer',
            'condition_upon_return_rating' => 'integer',
            'value_for_money_rating' => 'integer',

            // Booleans
            'is_verified_booking' => 'boolean',
            'is_flagged' => 'boolean',

            // Dates & Timestamps
            'responded_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * The polymorphic entity (Asset or User) being reviewed.
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reviewable_type', 'reviewable_id');
    }

    /**
     * The user who authored the review.
     */
    public function reviewer(): BelongsTo
    {
        // Explicitly mapping the misspelled migration column 'reviwer_id'
        return $this->belongsTo(User::class, 'reviwer_id');
    }

    /**
     * The booking transaction that provided the context for this review.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    /**
     * The user (reviewee) who replied to this review, if applicable.
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'response_from_reviewee_id');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    /**
     * Determine if this review includes detailed sub-category ratings.
     */
    protected function isDetailed(): Attribute
    {
        return Attribute::make(
            get: fn () => !is_null($this->cleanliness_rating) 
                       || !is_null($this->punctuality_rating) 
                       || !is_null($this->communication_rating)
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Scope to only include reviews tied to a fully verified platform booking.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified_booking', true)
                     ->where('is_flagged', false); // Never surface flagged reviews publicly
    }

    /**
     * Scope to isolate reviews flagged by the moderation system.
     */
    public function scopePendingModeration($query)
    {
        return $query->where('is_flagged', true);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================

    /**
     * Allow the reviewee to securely post a public response.
     */
    public function reply(string $text, User $responder): void
    {
        $this->update([
            'response_text' => $text,
            'response_from_reviewee_id' => $responder->id,
            'responded_at' => now(),
        ]);
    }

    /**
     * Flag a review for violating community guidelines.
     */
    public function flagForModeration(string $reason): void
    {
        $this->update([
            'is_flagged' => true,
            'flag_reason' => $reason,
        ]);
    }
}