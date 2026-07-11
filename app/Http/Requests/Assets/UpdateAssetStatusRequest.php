<?php

declare(strict_types=1);

namespace App\Http\Requests\Assets;

use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $asset = $this->route('asset');

        return $asset instanceof Asset && $this->user()->id === $asset->owner_id;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:'.implode(',', [StatusEnum::DELISTED->value, StatusEnum::PAUSED->value])],
            'reason' => ['required_if:status,StatusEnum::DELISTED', 'nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please provide the new status.',
            'status.in' => 'Status must be either PAUSED or DELISTED.',
            'reason.required_if' => 'Please provide a reason when delisting an asset.',
            'reason.max' => 'Reason cannot exceed 500 characters.',
        ];
    }
}
