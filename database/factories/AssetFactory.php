<?php

namespace Database\Factories;

use App\Domains\Shared\Enums\Asset\TypeEnum;
use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;
use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    public function definition(): array
    {
        $hourlyRate = fake()->randomFloat(2, 50, 2000);
        $dailyRate  = round($hourlyRate * 8, 2);
        $weeklyRate = round($dailyRate * 6, 2);

        $regions = [
            'Addis Ababa', 'Afar', 'Amhara', 'Benishangul-Gumuz',
            'Dire Dawa', 'Gambela', 'Harari', 'Oromia', 'Sidama',
            'Somali', 'South Ethiopia', 'Southwest Ethiopia',
            'Tigray', 'Central Ethiopia',
        ];

        return [
            'owner_id' => User::factory(),

            'title'       => fake()->randomElement([
                'CAT 320D Excavator', 'Toyota Hilux 4x4', 'Isuzu Dump Truck',
                'Honda Generator 5KVA', 'Bajaj Three Wheeler', 'Concrete Mixer',
                'John Deere Tractor', 'Scaffolding Set', 'Water Pump 3 inch',
                'Mitsubishi Forklift',
            ]) . ' #' . fake()->numberBetween(1, 999),

            'description' => fake()->paragraph(3),

            'asset_type' => fake()->randomElement(TypeEnum::cases()),
            'condition'  => fake()->randomElement(ConditionEnum::cases()),

            'hourly_rate'      => $hourlyRate,
            'daily_rate'       => $dailyRate,
            'weekly_rate'      => $weeklyRate,
            'monthly_rate'     => round($weeklyRate * 4, 2),
            'security_deposit' => round($dailyRate * fake()->randomFloat(1, 1, 3), 2),
            'estimated_value'  => round($dailyRate * fake()->numberBetween(50, 300), 2),

            'available_from'  => now()->subMonths(2),
            'available_until' => now()->addMonths(6),

            'region'             => fake()->randomElement($regions),
            'address_line'       => fake()->streetAddress(),
            'service_radius_km'  => fake()->randomElement([10, 25, 50, 75, 100]),
            'delivery_method'    => fake()->randomElement(DeliveryMethodEnum::cases()),

            'status' => fake()->randomElement([
                StatusEnum::ACTIVE, StatusEnum::ACTIVE, StatusEnum::ACTIVE,
                StatusEnum::ACTIVE, StatusEnum::DRAFT, StatusEnum::PAUSED,
            ]),

            // Why is this missing field the one that broke everything?
            // The migration defines visibility as enum NOT NULL with no
            // ->default() and no ->nullable(). Laravel's schema builder
            // doesn't auto-fill enums — if a factory doesn't explicitly
            // set every NOT NULL, no-default column, PostgreSQL rejects
            // the insert. This is exactly the kind of mismatch that only
            // shows up once you actually try to seed volume — your
            // manual UI testing never hit it because your Create form
            // probably hardcodes visibility to PUBLIC somewhere already.
            //
            // Why mostly PUBLIC?
            // Same reasoning as status being mostly ACTIVE — a real
            // marketplace has the vast majority of listings publicly
            // visible. PRIVATE/REGION_RESTRICTED/PARTNER_ONLY exist in
            // your schema for Phase 2 features that don't have UI yet,
            // so seeding a few of them is fine but they shouldn't dominate.
            'visibility' => fake()->randomElement([
                'PUBLIC', 'PUBLIC', 'PUBLIC', 'PUBLIC',
                'PRIVATE', 'REGION_RESTRICTED',
            ]),

            'created_at' => fake()->dateTimeBetween('-6 months', '-1 week'),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::ACTIVE,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusEnum::DRAFT,
        ]);
    }

    public function ownedBy(User $owner): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $owner->id,
        ]);
    }
}