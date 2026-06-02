<?php

namespace App\Models;

use App\Shared\Enums\TrustScore\TrustTier;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

// =====================
// CONFIGURATION
// =====================
#[Fillable([
    'user_id',
    'base_score',
    'completion_rate',
    'payment_punctuality',
    'asset_condition_score',
    'communication_score',
    'dispute_rate',
    'kyc_compliance_score',
    'final_score',
    'trust_tier',
    'risk_factors',
    'calculation_method',
    'calculation_timestamp',
    'next_recalculation_at',    
])]
#[Hidden([
    'risk_factors',
    'calculation_method',
])]
class TrustScore extends Model
{
    // Note: SoftDeletes is intentionally omitted here because the migration does not include `$table->softDeletes()`.
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
            // Precision Scoring (Financial-Grade Decimals)
            'base_score' => 'decimal:2',
            'completion_rate' => 'decimal:2',
            'payment_punctuality' => 'decimal:2',
            'asset_condition_score' => 'decimal:2',
            'communication_score' => 'decimal:2',
            'dispute_rate' => 'decimal:2',
            'kyc_compliance_score' => 'decimal:2',
            'final_score' => 'decimal:2',

            // Strict Enums
            'trust_tier' => TrustTier::class,

            // JSONB Payloads
            'risk_factors' => 'array',

            // Dates & Timestamps
            'calculation_timestamp' => 'datetime',
            'next_recalculation_at' => 'datetime',
        ];
    }

    // =====================
    // RELATIONSHIPS
    // =====================

    /**
     * The user this trust score belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =====================
    // COMPUTED ATTRIBUTES
    // =====================

    /**
     * Determine if the user's account requires immediate operational review based on risk flags.
     */
    protected function requiresManualReview(): Attribute
    {
        return Attribute::make(
            get: function () {
                $severity = $this->risk_factors['severity'] ?? null;
                return $severity === 'HIGH' || $severity === 'CRITICAL';
            }
        )->shouldCache();
    }

    // =====================
    // SCOPES (QUERY BUILDERS)
    // =====================

    /**
     * Scope to find scores that need to be recalculated by the background scheduler.
     */
    public function scopeDueForRecalculation($query)
    {
        return $query->whereNotNull('next_recalculation_at')
                     ->where('next_recalculation_at', '<=', now());
    }

    /**
     * Scope to quickly isolate high-trust platform members.
     */
    public function scopePremiumTrust($query)
    {
        return $query->whereIn('trust_tier', [TrustTier::GOLD, TrustTier::PLATINUM]);
    }

    // =====================
    // DOMAIN BUSINESS LOGIC
    // =====================

    /**
     * Apply the output of the Trust Engine recalculation job securely.
     */
    public function applyNewCalculation(float $finalScore, TrustTier $newTier, array $metadata = []): void
    {
        $this->update(array_merge([
            'final_score' => $finalScore,
            'trust_tier' => $newTier,
            'calculation_timestamp' => now(),
        ], $metadata));
    }
}