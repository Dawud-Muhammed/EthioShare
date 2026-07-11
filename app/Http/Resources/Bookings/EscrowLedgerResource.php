<?php

namespace App\Http\Resources\Bookings;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EscrowLedgerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'gateway_name' => $this->gateway_name,
            'transaction_id' => $this->transaction_id,

            // Postgres DECIMAL columns often come back from Eloquent as
            // STRINGS, not numbers -- Postgres does this on purpose, to
            // avoid floating-point rounding on money values internally.
            // But your frontend JS shouldn't have to guess whether
            // "amount_due" is "250.00" (string) or 250.00 (number) --
            // casting explicitly here means the API always promises a
            // real JSON number, consistently, no matter what Postgres
            // handed Eloquent underneath.
            'amount_due' => (float) $this->amount_due,
            'amount_held' => (float) $this->amount_held,
            'amount_released' => (float) $this->amount_released,
            'amount_refunded' => (float) $this->amount_refunded,

            // Your EscrowLedger model likely casts 'status' to
            // EscrowStatusEnum already (via $casts, same as your other
            // models). That means $this->status here isn't a raw
            // string -- it's already an enum OBJECT. ->value pulls the
            // actual string back out ('FUNDED', 'PENDING', etc.) so the
            // JSON response contains a plain string, not something that
            // looks like {"name": "FUNDED"} if left un-handled.
            'status' => $this->status?->value,

            // Already a structured array from the JSONB column --
            // nothing to transform, just pass it through.
            'transactions' => $this->transactions ?? [],

            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
