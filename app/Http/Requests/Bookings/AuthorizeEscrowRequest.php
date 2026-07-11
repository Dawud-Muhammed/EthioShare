<?php

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizeEscrowRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking');

        return $booking !== null && $this->user() !== null && $booking->renter_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [];
    }
}
