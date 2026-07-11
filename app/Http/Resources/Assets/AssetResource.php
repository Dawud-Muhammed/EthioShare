<?php

declare(strict_types=1);

namespace App\Http\Resources\Assets;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'owner' => [
                'id' => $this->owner_id,
                'name' => $this->owner->full_name ?? $this->owner->first_name,
                // ↑ We expose only what the client needs.
                // NOT the owner's email, phone, or private details.
                // TODO: Phase 2 — replace with UserResource for consistency
            ],

            'title' => $this->title,
            'description' => $this->description,
            'asset_type' => $this->asset_type,
            'condition' => $this->condition,

            'pricing' => [
                'hourly_rate' => $this->hourly_rate,
                'daily_rate' => $this->daily_rate,
                'weekly_rate' => $this->weekly_rate,
                'monthly_rate' => $this->monthly_rate,
                'security_deposit' => $this->security_deposit,
                // ↑ Grouping related fields under a key makes the API
                // easier to consume. The frontend gets pricing as one object.
            ],

            'location' => [
                'region' => $this->region,
                'address_line' => $this->address_line,
                'coordinates' => null,
                // ↑ NULL for now. Phase 2 this becomes [lat, lon] from PostGIS.
                // TODO: Phase 2 — extract from PostGIS point:
                // 'coordinates' => $this->location
                //     ? [$this->location->getLat(), $this->location->getLng()]
                //     : null,
            ],

            'availability' => [
                'available_from' => $this->available_from?->toDateString(),
                'available_until' => $this->available_until?->toDateString(),
                // ↑ The ?-> is the nullsafe operator.
                // If available_from is null, don't call toDateString(), just return null.
                // If it's a Carbon date, format it as "2026-06-01" (clean, no time).
            ],

            'delivery' => [
                'method' => $this->delivery_method,
                'service_radius_km' => $this->service_radius_km,
            ],

            'specifications' => $this->specifications,
            'features' => $this->features,

            'status' => $this->status,
            'visibility' => $this->visibility,

            'stats' => [
                'average_rating' => $this->average_rating,
                'total_reviews' => $this->total_reviews,
                'total_bookings' => $this->total_bookings,
                // ↑ These start at 0/null from your migration defaults.
                // They'll be updated by Phase 2 events.
            ],

            'created_at' => $this->created_at->toIso8601String(),
            // ↑ ISO 8601 format: "2026-06-06T14:30:00+00:00"
            // This is the international standard. Frontends and mobile apps expect this.
            // Never return raw Carbon objects — they serialize weirdly.

            'photos' => $this->whenLoaded('media', function () {
                return $this->media->map(fn ($media) => [
                    'id' => $media->id,
                    'url' => $media->public_url,
                    'is_primary' => $media->is_primary,
                    'file_name' => $media->file_name,
                ]);
            }, []),
        ];
    }
}
