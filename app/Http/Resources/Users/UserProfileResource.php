<?php

namespace App\Http\Resources\Users;

class UserProfileResource extends UserResource
{
    public function toArray($request): array
    {
        // Why array_merge with parent::toArray()?
        // Same pattern as BookingDetailResource. Everything in
        // UserResource (id, full_name, initials, trust signals)
        // is still here — we're adding the private fields on top.
        // If you ever change full_name's format in UserResource,
        // this resource gets that change for free.
        return array_merge(parent::toArray($request), [

            // =====================
            // EDITABLE IDENTITY
            // =====================
            // Why separate first_name/last_name here but not in
            // UserResource?
            // A settings form needs two separate input fields to edit.
            // full_name (from the parent) is a read-only display value —
            // you can't bind a single "full name" string back to two
            // database columns without splitting it yourself.
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,

            // =====================
            // CONTACT (private)
            // =====================
            // Why is this safe here but not in UserResource?
            // This resource is ONLY used for the authenticated user's
            // own profile. The controller calling this resource must
            // ensure $user->id === auth()->id() — that's an
            // authorization concern, not this resource's job, but it's
            // why this data is allowed to appear here at all.
            //
            // Why no decryption call needed?
            // Your model casts 'phone_number' => 'encrypted'. Laravel
            // decrypts automatically when you access $this->phone_number.
            // The resource just reads the attribute like any other.
            'email'          => $this->email,
            'email_verified' => !is_null($this->email_verified_at),
            'phone_number'   => $this->phone_number,

            // =====================
            // BUSINESS
            // =====================
            'business_type'                 => $this->business_type->value,
            'business_registration_number'  => $this->business_registration_number,

            // Why is_corporate_entity here?
            // It's a computed Attribute on your model — useful for the
            // frontend to conditionally show business-only form fields
            // (e.g. "Business registration number" input only appears
            // if is_corporate_entity is true).
            'is_corporate_entity' => $this->is_corporate_entity,

            // =====================
            // KYC
            // =====================
            // Why (int) on kyc_tier?
            // It's a SMALLINT in the database with no cast defined,
            // so it arrives as a string from some drivers. (int)
            // guarantees the frontend gets a number it can compare
            // (e.g. if (user.kyc_tier >= 2)).
            'kyc_tier'             => (int) $this->kyc_tier,
            'kyc_tier_verified_at' => $this->kyc_tier_verified_at?->toIso8601String(),

            // Why fayda_verified as a boolean, not the timestamp?
            // Same pattern as renter_reviewed/owner_reviewed in
            // BookingResource — the frontend cares "is this done?"
            // not "exactly when?"
            'fayda_verified' => !is_null($this->fayda_verified_at),

            // Why maskedFaydaId() instead of the raw fayda_id?
            // Even on the user's OWN profile, returning a full national
            // ID number in an API response is unnecessary exposure —
            // if this response is ever logged, cached, or the token is
            // compromised, the full Fayda ID leaks. Showing the last
            // 4 digits is enough for the user to confirm "yes, that's
            // the ID I submitted" without exposing the full number.
            // If you decide later you need the full value (e.g. for
            // an edit form), add a SEPARATE dedicated endpoint with
            // its own audit logging — don't widen this resource.
            'fayda_id_masked' => $this->maskedFaydaId(),

            // =====================
            // LOCATION
            // =====================
            // Why a nested 'location' object instead of flat fields?
            // These five fields all describe ONE concept — where this
            // person is. Grouping them makes the frontend's address
            // form component receive one object it can bind directly,
            // rather than five separate top-level keys.
            'location' => [
                'country_region' => $this->country_region,
                'city'           => $this->city,
                'address_line_1' => $this->address_line_1,
                'address_line_2' => $this->address_line_2,
                'postal_code'    => $this->postal_code,
            ],
            // TODO: Phase 2 — the 'location' column itself is a PostGIS
            // GEOGRAPHY(POINT) for geospatial search. Extracting
            // lat/lng here requires ST_X()/ST_Y() in the query or a
            // cast — out of scope until GeospatialSearchService exists.

            // =====================
            // ACCOUNT
            // =====================
            'account_status'        => $this->account_status->value,
            'is_two_factor_enabled' => (bool) $this->is_two_factor_enabled,

            // =====================
            // TRUST (own view can see the timestamp)
            // =====================
            'trust_score_updated_at' => $this->trust_score_updated_at?->toIso8601String(),

            // =====================
            // AUDIT
            // =====================
            'last_login_at' => $this->last_login_at?->toIso8601String(),
            'created_at'    => $this->created_at->toIso8601String(),
        ]);
    }

    /**
     * Why a private method instead of inline logic in toArray()?
     * toArray() should read like a list of fields. A multi-line
     * masking calculation inline would break that readability.
     * Pulling it into a named method also makes it independently
     * testable — you can write a unit test that calls
     * maskedFaydaId() directly with different fayda_id values.
     */
    private function maskedFaydaId(): ?string
    {
        // Why check null first?
        // fayda_id is nullable — a Tier 1 user hasn't submitted it yet.
        // Calling strlen(null) would throw a deprecation warning in
        // PHP 8.1+. Return null early and let the frontend show
        // "Not submitted" instead of "••••".
        if (is_null($this->fayda_id)) {
            return null;
        }

        // Why max(strlen - 4, 0)?
        // If somehow fayda_id is shorter than 4 characters (shouldn't
        // happen with real Fayda IDs, but defend anyway), strlen - 4
        // would be negative, and str_repeat() with a negative count
        // throws an error. max(..., 0) clamps it to zero — you'd get
        // no bullet characters and just the short string, which is
        // still safe and won't crash.
        return str_repeat('•', max(strlen($this->fayda_id) - 4, 0))
            . substr($this->fayda_id, -4);
    }
}