<?php

declare(strict_types=1);

namespace App\Http\Resources\Bookings;

use App\Http\Resources\Assets\AssetResource;
use App\Http\Resources\Users\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            // =====================
            // IDENTITY
            // =====================
            'id' => $this->id,

            // =====================
            // RELATIONSHIPS
            // =====================
            'asset' => new AssetResource($this->whenLoaded('asset')),
            'owner' => new UserResource($this->whenLoaded('owner')),
            'renter' => new UserResource($this->whenLoaded('renter')),

            // =====================
            // RENTAL PERIOD
            // =====================
            'start_datetime' => $this->start_datetime->toIso8601String(),
            'end_datetime' => $this->end_datetime->toIso8601String(),
            'actual_start_datetime' => $this->actual_start_datetime?->toIso8601String(),
            'actual_end_datetime' => $this->actual_end_datetime?->toIso8601String(),

            // =====================
            // PRICING
            // =====================
            'rate_applied' => (float) $this->rate_applied,
            'total_rental_amount' => (float) $this->total_rental_amount,
            'security_deposit_amount' => (float) $this->security_deposit_amount,
            'platform_fee' => (float) $this->platform_fee,
            'total_charged' => (float) $this->total_charged,

            // =====================
            // STATUS
            // =====================
            'booking_status' => $this->booking_status->value,
            'payment_status' => $this->payment_status->value,
            'escrow_status' => $this->escrow_status->value,

            // =====================
            // HANDOFF
            // =====================
            'handoff_method' => $this->handoff_method->value,
            'handoff_location' => $this->whenLoaded('handoffLocation'),
            'handoff_completed_at' => $this->handoff_completed_at?->toIso8601String(),

            // =====================
            // REVIEW SIGNALS
            // =====================
            'renter_reviewed' => ! is_null($this->renter_review_submitted_at),
            'owner_reviewed' => ! is_null($this->owner_review_submitted_at),

            // =====================
            // AUDIT
            // =====================
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // TODO: Phase 2 — add escrow_ledger, dispute, and reviews
            // when those modules are built.
            // 'escrow_ledger' => new EscrowLedgerResource(
            //     $this->whenLoaded('escrowLedger')
            // ),
            // 'dispute' => new DisputeResource(
            //     $this->whenLoaded('dispute')
            // ),
        ];
    }
}
