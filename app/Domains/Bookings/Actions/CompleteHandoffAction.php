<?php

declare(strict_type=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Models\Booking;
use App\Models\User;

class CompleteHandoffAction
{
    public function execute(Booking $booking, User $user): Booking
    {
        if ($booking->owner_id !== $user->id) {
            throw new UnauthorizedBookingActionException(
                'only the booker can confirm that the handoff is complated'
            );
        }

        if ($booking->booking_status !== BookingStatusEnum::RENTER_ARRIVED) {
            throw new InvalidStateTransitionException(
                'the renter must be arrived before the handoff'
            );
        }

        $booking->update([
            'booking_status' => BookingStatusEnum::IN_PROGRESS,
        ]);

        return $booking->refresh();
    }
}
