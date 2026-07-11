<?php

declare(strict_types=1);

namespace App\Http\Resources\Bookings;

class BookingDetailResource extends BookingResource
{
    public function toArray($request): array
    {
        return array_merge(parent::toArray($request), [
            'handoff_location' => $this->whenLoaded('handoffLocation'),
            'handoff_verified_by' => $this->whenLoaded('handoffVerifier'),
            'has_dispute' => ! is_null($this->dispute_id),
            'dispute_id' => $this->dispute_id,

            // TODO: Phase 2 — full dispute resource
            // 'dispute' => new DisputeResource(
            //     $this->whenLoaded('dispute')
            // ),
        ]);
    }
}
