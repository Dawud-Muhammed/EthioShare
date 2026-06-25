<?php

declare(strict_types=1);

namespace App\Http\Requests\Assets;

use Illuminate\Foundation\Http\FormRequest;

use App\Domains\Shared\Enums\Asset\VisibilityEnum;
use App\Domains\Shared\Enums\Asset\TypeEnum;
use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;

class CreateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
        // TODO: Phase 2 - check auth()->user()->kyc_tier >= 2 for machinery
    }

    public function rules(): array
    {
        return [

            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],

            'asset_type' => ['required', 'string', 'in:' . implode(',', TypeEnum::values())],
            'condition' => ['required', 'string', 'in:' . implode(',', ConditionEnum::values())],


            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],

            'weekly_rate' => ['nullable', 'numeric', 'min:0'],            
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            // TODO: Phase 2 - weekly_rate and monthly_rate should auto-calculate if not provided

            'security_deposit' => ['required', 'numeric', 'min:0'],
            'estimated_value' => ['required', 'numeric', 'min:0'],
            // ↑ Required for escrow calculations in Phase 2. Store it now.

            'available_from' => ['nullable', 'date', 'after_or_equal:today'],
            'available_until' => ['nullable', 'date', 'after:available_from'],


            'region' => ['required', 'string', 'max:100'],
            'address_line' => ['required', 'string', 'max:255'],
            // TODO: Phase 2 - geocode this address into PostGIS POINT for location column

            'delivery_method' => ['nullable', 'string', 'in:' . implode(',', DeliveryMethodEnum::values())],
            // TODO: Phase 2 - Phase 2 this becomes required before publishing.

            'service_radius_km' => ['nullable', 'numeric', 'min:1', 'max:500'],
            // TODO: Phase 2 - validate against Ethiopian geographic boundaries

            // --- PHASE 2 FIELDS (accept anything valid, don't over-validate yet) ---

            'specifications' => ['nullable', 'array'],
            // ↑ JSONB column. We accept any key-value pairs for now.
            // TODO: Phase 2 - validate structure per asset_type
            // e.g., MACHINERY requires brand, model, year

            'features' => ['nullable', 'array'],
            // ↑ Array of strings like ['GPS_ENABLED', 'REFRIGERATED']
            'features.*' => ['string', 'max:50'],
            // The * means "apply this rule to EVERY item in the array"
            // features.0, features.1, features.2... all must be strings under 50 chars
            // TODO: Phase 2 — validate against a FeatureEnum

            'visibility' => ['nullable', 'string', 'in:'. implode(', ', VisibilityEnum::values())],
            // ↑ Defaults to PUBLIC in the action if not sent.
        ];
    }

    public function messages(): array
    {
    return [
            'title.required'            => 'Please give your asset a title.',
            'title.min'                 => 'Title must be at least 5 characters.',
            'title.max'                 => 'Title cannot exceed 255 characters.',
            'asset_type.required'       => 'Asset type is required.',
            'asset_type.in'             => 'Invalid asset type. Accepted: ' . implode(', ', TypeEnum::values()),
            'condition.required'        => 'Condition is required.',
            'condition.in'              => 'Invalid condition. Accepted: ' . implode(', ', ConditionEnum::values()),
            'hourly_rate.required'      => 'Hourly rate is required.',
            'hourly_rate.min'           => 'Hourly rate cannot be negative.',
            'daily_rate.required'       => 'Daily rate is required.',
            'security_deposit.required' => 'Security deposit is required.',
            'estimated_value.required'  => 'Estimated asset value is required.',
            'region.required'           => 'Please select an Ethiopian region.',
            'address_line.required'     => 'Please enter the asset pickup address.',
            'available_until.after'     => 'End date must be after start date.',
            'delivery_method.in'        => 'Invalid delivery method. Accepted: ' . implode(', ', DeliveryMethodEnum::values()),
        ];
    }
}