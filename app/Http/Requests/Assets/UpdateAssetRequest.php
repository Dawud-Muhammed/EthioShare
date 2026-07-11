<?php

declare(strict_types=1);

namespace App\Http\Requests\Assets;

use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;
use App\Domains\Shared\Enums\Asset\TypeEnum;
use App\Domains\Shared\Enums\Asset\VisibilityEnum;
use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        $asset = $this->route('asset');

        return $asset instanceof Asset && $this->user()->id === $asset->owner_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:5', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:5000'],

            'asset_type' => ['sometimes', 'string', 'in:'.implode(',', TypeEnum::values())],
            'condition' => ['sometimes', 'string', 'in:'.implode(',', ConditionEnum::values())],

            'hourly_rate' => ['sometimes', 'numeric', 'min:0'],
            'daily_rate' => ['sometimes', 'numeric', 'min:0'],

            'weekly_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'monthly_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],

            'security_deposit' => ['sometimes', 'numeric', 'min:0'],
            'estimated_value' => ['sometimes', 'numeric', 'min:0'],

            'available_from' => ['sometimes', 'nullable', 'date', 'after_or_equal:today'],
            'available_until' => ['sometimes', 'nullable', 'date', 'after:available_from'],

            'region' => ['sometimes', 'string', 'max:100'],
            'address_line' => ['sometimes', 'string', 'max:255'],

            'delivery_method' => ['sometimes', 'nullable', 'string', 'in:'.implode(',', DeliveryMethodEnum::values())],

            'service_radius_km' => ['sometimes', 'nullable', 'numeric', 'min:1', 'max:500'],

            'specifications' => ['sometimes', 'nullable', 'array'],

            'features' => ['sometimes', 'nullable', 'array'],
            'features*' => ['string', 'max:50'],

            'visibility' => ['sometimes', 'nullable', 'string', 'in:'.implode(',', VisibilityEnum::values())],

            'photos' => ['sometimes', 'array', 'min:1', 'max:10'],
            // ↑ 'photos' is the field name the frontend sends.

            'photos.*' => ['sometimes', 'file', 'image', 'mimes:png,jpg,webp,jpeg', 'max:10240'],
            'primary_index' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:9'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.min' => 'Title must be at least 5 characters.',
            'hourly_rate.min' => 'Hourly rate cannot be negative.',
            'daily_rate.min' => 'Daily rate cannot be negative.',
            'available_until.after' => 'End date must be after start date.',
            'asset_type.in' => 'Invalid asset type. Accepted: '.implode(', ', TypeEnum::values()),
            'condition.in' => 'Invalid condition. Accepted: '.implode(', ', ConditionEnum::values()),
            'delivery_method.in' => 'Invalid delivery method. Accepted: '.implode(', ', DeliveryMethodEnum::values()),

            'photos.max' => 'You can upload a maximum of 10 photos at once.',
            'photos.*.image' => 'Each file must be an image.',
            'photos.*.mimes' => 'Photos must be JPG, JPEG, PNG, or WebP format.',
            'photos.*.max' => 'Each photo must be under 10MB.',
            'primary_index.max' => 'Primary index cannot exceed the number of photos.',
        ];
    }
}
