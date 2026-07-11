<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Models\Booking;
use App\Models\User;

class ConfirmRenterArrivalAction
{
    public function execute(Booking $booking, User $user): Booking
    {
        if ($booking->renter_id !== $user->id) {
            throw new UnauthorizedBookingActionException(
                'Only the renter can confirm that the Booking Owner is arrived.'
            );
        }

        if ($booking->booking_status !== BookingStatusEnum::CONFIRMED) {
            throw new InvalidStateTransitionException(
                'Cannot a renter arrived for a booking with status: '.$booking->booking_status->value
            );
        }

        $booking->update([
            'booking_status' => BookingStatusEnum::RENTER_ARRIVED,
            'actual_start_datetime' => now(),
        ]);

        return $booking->refresh();
    }
}
