<?php

declare(strict_types=1);

namespace App\Http\Requests\Bookings;

use Illuminate\Foundation\Http\FormRequest;

class InitiateReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'min:10', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Please select a star rating.',
            'rating.integer' => 'Rating must be a whole number.',
            'rating.min' => 'Rating must be at least 1 star.',
            'rating.max' => 'Rating cannot exceed 5 stars.',
            'comment.min' => 'Your comment must be at least 10 characters.',
            'comment.max' => 'Your comment cannot exceed 2000 characters.',
        ];
    }
}
