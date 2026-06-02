<?php

namespace App\Models;

use \App\Shared\Enums\EscrowLedger\GatewayName;
use \App\Shared\Enums\EscrowLedger\OwnerPayoutStatus;
use \App\Shared\Enums\EscrowLedger\EscrowLedgerStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
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
    'transaction_id',
    'gateway_name',
    'gateway_transaction_id',
    'amount_due',
    'amount_held',
    'amount_released',
    'amount_refunded',
    'transactions',
    'owner_payout_status',
    'owner_payout_at',
    'owner_account_id',
    'status',
    'status_updated_at',
    'expires_at',    
])]

// =====================
// CASTING PIPELINE
// =====================
#[Hidden([
    'owner_account_id',
    'deleted_at',    
])]
class EscrowLedger extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Security: AES-256-GCM Native Encryption at Rest
            'owner_account_id' => 'encrypted',

            // JSONB Structured Payloads (Event Sourcing)
            'transactions' => 'array',

            // Strict Enums (State Machines)
            'gateway_name' => GatewayName::class,
            'owner_payout_status' => OwnerPayoutStatus::class,
            'status' => EscrowLedgerStatus::class,

            // Financial Immutability
            'amount_due' => 'decimal:2',
            'amount_held' => 'decimal:2',
            'amount_released' => 'decimal:2',
            'amount_refunded' => 'decimal:2',

            // Dates & Timestamps
            'owner_payout_at' => 'datetime',
            'status_udated_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * Get the booking associated with this escrow ledger.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    /**
     * Calculate the remaining balance held in escrow that has not been released or refunded.
     */
    protected function remainingBalance(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0, $this->amount_held - ($this->amount_released + $this->amount_refunded))
        )->shouldCache();
    }

    /**
     * Determine if the escrow is fully funded against the amount due.
     */
    protected function isFullyFunded(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->amount_held >= $this->amount_due
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Scope a query to only include ledgers awaiting owner payout.
    */
    public function scopePendingPayout($query)
    {
        return $query->where('owner_payout_status', OwnerPayoutStatus::PENDING)
                     ->where('status', EscrowLedgerStatus::RELEASED)
                     ->where('amount_released', '>', 0);
    }

    /**
     * Scope a query to find ledgers that require automatic refund processing.
    */
    public function scopeExpiredAndUncaptured($query)
    {
        return $query->where('status', EscrowLedgerStatus::FUNDED)
                     ->whereNotNull('expires_at')
                     ->where('expires_at', '<=', now());
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================

    /**
     * Record a new financial event to the immutable JSONB ledger.
    */
    public function recordTransaction(array $transactionDetails): void
    {
        $transactions = $this->transactions ?? [];
        $transactions[] = array_merge([
            'timestamp' => now()->toIso8601String(),
        ], $transactionDetails);

        $this->update(['transactions' => $transactions]);
    }
}