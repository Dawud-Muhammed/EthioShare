<?php
declare(strict_types=1);

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class CancelBookingRequest extends FormRequest{
    public function authorize(){
        return true;
    }

    public function rules(): array{
        return [
            'reason' => ['required', 'string', 'min:10', 'max:500']
        ];
    }
}