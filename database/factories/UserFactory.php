<?php

namespace Database\Factories;

use App\Domains\Shared\Enums\User\AccountStatusEnum;
use App\Domains\Shared\Enums\User\BusinessTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone_number' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'fayda_id' => fake()->unique()->numerify('############'),
            'fayda_verified_at' => now(),
            'kyc_tier' => fake()->numberBetween(1, 3),
            'kyc_tier_verified_at' => now(),
            'kyc_metadata' => [
                'verified_documents' => ['national_id', 'selfie'],
                'verification_date' => now()->toDateString(),
            ],
            'business_type' => fake()->randomElement([
                BusinessTypeEnum::INDIVIDUAL,
                BusinessTypeEnum::SME,
                BusinessTypeEnum::CORPORATIVE,
            ]),
            'account_status' => AccountStatusEnum::ACTIVE,
            'is_verified' => true,
            'is_two_factor_enabled' => false,
            'total_trust_score' => fake()->randomFloat(2, 0, 100),
            'trust_score_updated_at' => now(),
            'last_login_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
            'is_verified' => false,
        ]);
    }

    /**
     * Indicate that the user is a business entity.
     */
    public function business(): static
    {
        return $this->state(fn (array $attributes) => [
            'business_name' => fake()->company(),
            'business_registration_number' => fake()->unique()->bothify('BR-##########'),
            'business_type' => fake()->randomElement([
                BusinessTypeEnum::SME,
                BusinessTypeEnum::CORPORATIVE,
                BusinessTypeEnum::COOPERATIVE,
            ]),
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_two_factor_enabled' => true,
            'two_factor_secret' => encrypt('secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['recovery-code-1'])),
            'two_factor_confirmed_at' => now(),
        ]);
    }
}
