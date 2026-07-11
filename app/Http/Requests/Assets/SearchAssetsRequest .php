<?php

declare(strict_types=1);

namespace App\Http\Requests\Assets;

use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;
use App\Domains\Shared\Enums\Asset\TypeEnum;
use Illuminate\Foundation\Http\FormRequest;

class SearchAssetsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'region' => ['sometimes', 'string', 'max:100'],
            'asset_type' => ['sometimes', 'string', 'in:'.implode(',', TypeEnum::values())],
            'condition' => ['sometimes', 'string', 'in:'.implode(',', ConditionEnum::values())],
            'delivery_method' => ['sometimes', 'string', 'in:'.implode(',', DeliveryMethodEnum::values())],
            'min_price' => ['sometimes', 'numeric', 'min:0'],
            'max_price' => ['sometimes', 'numeric', 'min:0', 'gt:min_price'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_type.in' => 'Invalid asset type. Accepted: '.implode(', ', TypeEnum::values()),
            'condition.in' => 'Invalid condition. Accepted: '.implode(', ', ConditionEnum::values()),
            'delivery_method.in' => 'Invalid delivery method. Accepted: '.implode(', ', DeliveryMethodEnum::values()),
            'max_price.gt' => 'Maximum price must be greater than minimum price.',
            'per_page.max' => 'Cannot request more than 50 assets per page.',
        ];
    }
}
