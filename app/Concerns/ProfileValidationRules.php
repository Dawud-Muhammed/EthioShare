<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(?string $userId = null): array
    {
        return [
            'email' => $this->emailRules($userId),
            'phone_number' => $this->phoneRules($userId),
            'first_name' => $this->firstNameRules(),
            'last_name' => $this->lastNameRules(),
        ];
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function firstNameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    protected function lastNameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?string $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:255',
            $userId === null
                ? Rule::unique(User::class, 'email')
                : Rule::unique(User::class, 'email')->ignore($userId, 'id'),
        ];
    }

    /**
     * Get the validation rules used to validate user Phone numbers.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function phoneRules(?string $userId = null): array
    {
        return [
            'required', 
            'string',
            'regex:/^(?:\+251|0)[97]\d{8}$/',
            $userId === null
                ? Rule::unique(User::class, 'phone_number')
                : Rule::unique(User::class, 'phone_number')->ignore($userId, 'id'),
        ];
    }
}