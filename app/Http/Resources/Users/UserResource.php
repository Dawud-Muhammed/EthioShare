<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            // =====================
            // IDENTITY
            // =====================
            'id' => $this->id,

            // Why full_name and not first_name/last_name separately?
            // This resource is for OTHER people looking at this user.
            // "Jane Smith" is what a renter sees on a booking card.
            // They don't need first_name and last_name as separate
            // fields — that's an editing concern, not a display concern.
            // full_name is the computed Attribute from your model
            // (Attribute::make()->shouldCache()), so it's cheap to call.
            'full_name' => $this->full_name,

            // Why initials()?
            // initials() is a plain method (not an Attribute), so it's
            // called with (). Useful for avatar placeholders — "JS"
            // in a circle when there's no profile photo yet.
            'initials' => $this->initials(),

            // =====================
            // BUSINESS IDENTITY
            // =====================
            // Why business_name here?
            // If Jane is renting as "Addis Logistics PLC" rather than
            // as herself, the other party should see that. For
            // INDIVIDUAL accounts this column is null, so it simply
            // won't appear as a meaningful value — that's fine,
            // the frontend can check for null and fall back to full_name.
            'business_name' => $this->business_name,

            // =====================
            // TRUST SIGNALS
            // =====================
            // Why is_verified is safe to expose:
            // This is exactly the kind of signal other users NEED to
            // make a decision — "is this person verified?" It's a
            // boolean, not raw KYC data. Safe by design.
            'is_verified' => (bool) $this->is_verified,

            // Why total_trust_score with a null check?
            // New users start at a default score (per your Phase 2 spec,
            // base_score defaults to 50), but until TrustScore
            // initialization runs (Phase 2 TODO), total_trust_score
            // on the users table could be 0 or null depending on your
            // seeder. The null check prevents (float) null → 0.0
            // silently looking like a real score of zero.
            'total_trust_score' => ! is_null($this->total_trust_score)
                ? (float) $this->total_trust_score
                : null,

            // Why whenLoaded('trustScores') here?
            // trustScores is a HasOne relation on your User model.
            // Same N+1 reasoning as BookingResource — if a controller
            // didn't eager load it, we don't want a query firing for
            // every user in a list. If it WAS loaded, we read
            // trust_tier off the related TrustScore model.
            //
            // Why the double null-safe chain (?->trust_tier?->value)?
            // First ?->: the user might not have a TrustScore row yet
            // (Phase 1, before InitializeTrustScoreAction exists).
            // Second ?->: even if the row exists, trust_tier could
            // theoretically be null before first calculation.
            // Without both, a brand new user would crash this resource.
            'trust_tier' => $this->whenLoaded(
                'trustScores',
                fn () => $this->trustScores?->trust_tier?->value
            ),

            // =====================
            // TENURE SIGNAL
            // =====================
            // Why format('Y-m') and not the full timestamp?
            // "Member since 2025-03" is a trust signal shown publicly.
            // The exact day and time someone registered is not useful
            // information for other users and adds noise.
            'member_since' => $this->created_at?->format('Y-m'),

            // TODO: Phase 2 — profile photo via $this->media()
            // (mediable_type = 'User', purpose = PROFILE_PHOTO)
        ];
    }
}
