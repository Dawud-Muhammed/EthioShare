<?php
declare(strict_types = 1);

namespace App\Http\Requests\Bookings;

use App\Domains\Shared\Enums\Booking\HandoffMethodEnum;
use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest{
    public function authorize(): bool{
        $asset = Asset::find($this->input('asset_id'));
        if(!$asset){
            return true;
        }
        return $asset->owner_id !== $this->user()->id;
    }

    public function rules(): array{
        return [
            'asset_id' => ['required', 'string', 'exists:assets,id'],
            'start_datetime' => ['required', 'date', 'after:now'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'handoff_method' => ['required', 'string', 'in:'. implode(', ' , HandoffMethodEnum::values())]
        ];
    }
    public function messages(): array
    {
        return [
            'asset_id.required' => 'Please select an asset to book.',
            'asset_id.exists'   => 'This asset no longer exists.',
            'start_datetime.required' => 'Please choose a start date and time.',
            'start_datetime.after'    => 'The start time must be in the future.',
            'end_datetime.required'   => 'Please choose an end date and time.',
            'end_datetime.after'      => 'The end time must be after the start time.',
            'handoff_method.required' => 'Please choose how you will collect the asset.',
            'handoff_method.in'       => 'Please choose a valid handoff method.',
        ];
    }
}