<?php

namespace Database\Factories;

use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Domains\Shared\Enums\Booking\EscrowStatusEnum;
use App\Domains\Shared\Enums\Booking\HandoffMethodEnum;
use App\Domains\Shared\Enums\Booking\PaymentStatusEnum;
use App\Models\Asset;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        // Why is the base definition() PENDING and minimal?
        // Every other status is built as a STATE on top of this base
        // (see below). definition() should represent the simplest,
        // earliest-lifecycle version of a booking — the moment it's
        // first created, before anything else has happened to it.
        // This mirrors your actual CreateBookingAction output exactly.

        $start = Carbon::instance(fake()->dateTimeBetween('-3 months', '+1 month'));
        $end   = (clone $start)->addHours(fake()->numberBetween(2, 72));

        $hours       = ceil($start->diffInHours($end));
        $rateApplied = fake()->randomFloat(2, 50, 500);
        $rentalAmount = $hours * $rateApplied;
        $platformFee  = round($rentalAmount * 0.075, 2);
        $deposit      = fake()->randomFloat(2, 200, 2000);

        return [
            // Why Asset::factory() and User::factory() as defaults?
            // Same reasoning as owner_id in AssetFactory — if the
            // seeder doesn't explicitly pass asset_id/renter_id,
            // Laravel creates fresh fake records automatically.
            // In practice your seeder WILL always pass these explicitly
            // (see BookingSeeder below) because renter_id must never
            // equal owner_id — that's a real guard in your own code.
            'asset_id'  => Asset::factory(),
            'renter_id' => User::factory(),
            'owner_id'  => User::factory(),

            'start_datetime' => $start,
            'end_datetime'   => $end,

            // Why null here?
            // A freshly created PENDING booking has no actual times yet
            // — the renter hasn't arrived, nothing has happened.
            // States below fill these in for later statuses.
            'actual_start_datetime' => null,
            'actual_end_datetime'   => null,

            'rate_applied'            => $rateApplied,
            'total_rental_amount'     => $rentalAmount,
            'security_deposit_amount' => $deposit,
            'platform_fee'            => $platformFee,
            'total_charged'           => $rentalAmount + $platformFee + $deposit,

            'booking_status' => BookingStatusEnum::PENDING,
            'payment_status' => PaymentStatusEnum::PENDING,
            'escrow_status'  => EscrowStatusEnum::PENDING,

            'handoff_method' => fake()->randomElement(HandoffMethodEnum::cases()),

            'created_at' => $start->copy()->subDays(fake()->numberBetween(1, 14)),
        ];
    }

    /**
     * Why states for EVERY status instead of one big random factory?
     * This is the core lesson from our last conversation. A COMPLETED
     * booking must have actual_start_datetime AND actual_end_datetime
     * filled in, or your BookingShow timeline and resources will show
     * broken/contradictory data. Each state below guarantees internal
     * consistency — exactly what CreateBookingAction → ConfirmBookingAction
     * → ... → CompleteBookingAction would produce if a real user walked
     * through the full lifecycle by hand.
     */

    public function pending(): static
    {
        // Why is this state basically empty?
        // definition() above already IS the pending state.
        // This state exists purely for readability when chaining —
        // Booking::factory()->pending()->create() is self-documenting
        // even though it changes nothing.
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatusEnum::PENDING,
            'payment_status' => PaymentStatusEnum::PENDING,
            'escrow_status'  => EscrowStatusEnum::PENDING,
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatusEnum::CONFIRMED,
            'payment_status' => PaymentStatusEnum::AUTHORIZED,
            'escrow_status'  => EscrowStatusEnum::FUNDED,
        ]);
    }

    public function renterArrived(): static
    {
        return $this->state(function (array $attributes) {
            // Why reference $attributes['start_datetime'] here?
            // States run AFTER definition(), so $attributes already
            // contains the start_datetime this specific booking got.
            // actual_start_datetime should land near the planned start,
            // not at some random unrelated time.
            $start = Carbon::parse($attributes['start_datetime']);

            return [
                'booking_status'        => BookingStatusEnum::RENTER_ARRIVED,
                'payment_status'        => PaymentStatusEnum::AUTHORIZED,
                'escrow_status'         => EscrowStatusEnum::FUNDED,
                'actual_start_datetime' => $start->copy()->addMinutes(fake()->numberBetween(0, 20)),
            ];
        });
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $start = Carbon::parse($attributes['start_datetime']);

            return [
                'booking_status'        => BookingStatusEnum::IN_PROGRESS,
                'payment_status'        => PaymentStatusEnum::CAPTURED,
                'escrow_status'         => EscrowStatusEnum::HELD,
                'actual_start_datetime' => $start->copy()->addMinutes(fake()->numberBetween(0, 20)),
                'handoff_completed_at'  => $start->copy()->addMinutes(fake()->numberBetween(0, 20)),
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $start = Carbon::parse($attributes['start_datetime']);
            $end   = Carbon::parse($attributes['end_datetime']);

            // Why sometimes push actual_end_datetime past end_datetime?
            // This is exactly the "late return" scenario from our last
            // conversation. You're not building punishment logic yet —
            // you're just generating REALISTIC test data so that when
            // you DO build the late-return dispute feature later, you
            // already have seeded bookings to test it against.
            $isLate = fake()->boolean(15); // 15% of completed bookings are late
            $actualEnd = $isLate
                ? $end->copy()->addHours(fake()->numberBetween(1, 48))
                : $end->copy()->subMinutes(fake()->numberBetween(0, 30));

            return [
                'booking_status'             => BookingStatusEnum::COMPLETED,
                'payment_status'             => PaymentStatusEnum::CAPTURED,
                'escrow_status'              => EscrowStatusEnum::RELEASED,
                'actual_start_datetime'      => $start->copy()->addMinutes(fake()->numberBetween(0, 20)),
                'actual_end_datetime'        => $actualEnd,
                'handoff_completed_at'       => $start->copy()->addMinutes(fake()->numberBetween(0, 20)),
                'renter_review_submitted_at' => fake()->boolean(60) ? $actualEnd->copy()->addHours(fake()->numberBetween(1, 72)) : null,
                'owner_review_submitted_at'  => fake()->boolean(50) ? $actualEnd->copy()->addHours(fake()->numberBetween(1, 72)) : null,
            ];
        });
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => BookingStatusEnum::CANCELLED,
            'payment_status' => fake()->randomElement([
                PaymentStatusEnum::PENDING,
                PaymentStatusEnum::REFUNDED,
            ]),
            'escrow_status' => fake()->randomElement([
                EscrowStatusEnum::PENDING,
                EscrowStatusEnum::RELEASED,
            ]),
        ]);
    }

    /**
     * Why dedicated renter/owner states like ownedBy() on AssetFactory?
     * You will constantly need "give me a booking where THIS specific
     * user is the renter" to log in as that user and verify the UI.
     * Without this, you'd manually override renter_id every call.
     */
    public function asRenter(User $renter): static
    {
        return $this->state(fn (array $attributes) => [
            'renter_id' => $renter->id,
        ]);
    }

    public function asOwner(User $owner): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_id' => $owner->id,
        ]);
    }
}