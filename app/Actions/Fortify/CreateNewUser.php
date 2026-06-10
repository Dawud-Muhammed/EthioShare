<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use \App\Domains\Shared\Enums\User\AccountStatusEnum;
use \App\Domains\Shared\Enums\User\BusinessTypeEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
public function create(array $input): User
{
    Validator::make($input, [
        ...$this->profileRules(),         
        'password' => $this->passwordRules(),
    ])->validate();

    return User::create([
        'first_name'         => $input['first_name'],
        'last_name'          => $input['last_name'],
        'email'              => $input['email'],
        'phone_number'       => $input['phone_number'], // FIXED: Matches both form input and database schema
        'password'           => Hash::make($input['password']),

        // Foundational Defaults
        'business_type'      => BusinessTypeEnum::INDIVIDUAL,
        'account_status'     => AccountStatusEnum::ACTIVE,
        'is_verified'        => false,
        'kyc_tier'           => 1,
        'kyc_metadata'       => [
            'registration_method' => 'web_form',
            'device_ip'           => request()->ip(),
        ],
        'total_trust_score'  => 50.00,
    ]);
}
}
