<?php

namespace App\Models;

use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Domains\Shared\Enums\Booking\EscrowStatusEnum;
use App\Domains\Shared\Enums\Booking\HandoffMethodEnum;
use App\Domains\Shared\Enums\Booking\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

// =====================
// CONFIGURATION
// =====================
#[Fillable([
    'asset_id',
    'renter_id',
    'owner_id',
    'start_datetime',
    'end_datetime',
    'actual_start_datetime',
    'actual_end_datetime',
    'rate_applied',
    'total_rental_amount',
    'security_deposit_amount',
    'platform_fee',
    'total_charged',
    'booking_status',
    'payment_status',
    'escrow_status',
    'handoff_method',
    'handoff_location_id',
    'handoff_qr_code',
    'handoff_completed_at',
    'handoff_verified_by',
    'renter_review_submitted_at',
    'owner_review_submitted_at',
    'dispute_id',
])]
#[Hidden([
    'handoff_qr_code', // Contains sensitive digital access payload
    'deleted_at',
])]
class Booking extends Model
{
    use HasFactory, HasFactory, HasUlids, SoftDeletes;
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
            // Security: AES-256-GCM Native Encryption at Rest
            'handoff_qr_code' => 'encrypted',

            // Strict Enums (State Machines)
            'booking_status' => BookingStatusEnum::class,
            'payment_status' => PaymentStatusEnum::class,
            'escrow_status' => EscrowStatusEnum::class,
            'handoff_method' => HandoffMethodEnum::class,

            // Immutability & Financial Decimals
            'rate_applied' => 'decimal:2',
            'total_rental_amount' => 'decimal:2',
            'security_deposit_amount' => 'decimal:2',
            'platform_fee' => 'decimal:2',
            'total_charged' => 'decimal:2',

            // Dates & Timestamps
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'actual_start_datetime' => 'datetime',
            'actual_end_datetime' => 'datetime',
            'handoff_completed_at' => 'datetime',
            'renter_review_submitted_at' => 'datetime',
            'owner_review_submitted_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * The asset being rented in this transaction.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * The user renting the asset.
     */
    public function renter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'renter_id');
    }

    /**
     * The user who owns the asset.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The physical location designated for handoff.
     */
    public function handoffLocation(): BelongsTo
    {
        return $this->belongsTo(HandoffLocation::class, 'handoff_location_id');
    }

    /**
     * The user (often the owner or a platform proxy) who verified the QR/Handoff.
     */
    public function handoffVerifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handoff_verified_by');
    }

    /**
     * The dispute associated with this booking, if one was raised.
     */
    public function dispute(): BelongsTo
    {
        return $this->belongsTo(Dispute::class, 'dispute_id');
    }

    /**
     * The financial escrow ledger attached to this booking.
     */
    public function escrowLedger(): HasOne
    {
        return $this->hasOne(EscrowLedger::class, 'booking_id');
    }

    /**
     * Polymorphic reviews left for this specific booking instance.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    /**
     * Calculate the planned duration of the rental in hours.
     */
    protected function plannedDurationHours(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->start_datetime || ! $this->end_datetime) {
                    return 0;
                }

                return $this->start_datetime->diffInHours($this->end_datetime);
            }
        )->shouldCache();
    }

    /**
     * Determine if the booking is currently active and in possession of the renter.
     */
    protected function isActive(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->booking_status === BookingStatusEnum::IN_PROGRESS
                       && $this->payment_status === PaymentStatusEnum::CAPTURED
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Scope to find bookings that are active right now.
     */
    public function scopeActive($query)
    {
        return $query->where('booking_status', BookingStatusEnum::IN_PROGRESS);
    }

    /**
     * Scope to find bookings where the end time has passed but the asset isn't returned.
     */
    public function scopeOverdue($query)
    {
        return $query->where('booking_status', BookingStatusEnum::IN_PROGRESS)
            ->where('end_datetime', '<', now())
            ->whereNull('actual_end_datetime');
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================

    /**
     * Initiate the handoff phase, locking the state.
     */
    public function confirmRenterArrival(): void
    {
        $this->update([
            'booking_status' => BookingStatusEnum::RENTER_ARRIVED,
            // Additional business logic like triggering events would happen in your Action classes
        ]);
    }
}
