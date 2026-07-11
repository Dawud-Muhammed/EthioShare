<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Models\Booking;
use App\Models\User;

class CompleteBookingAction
{
    public function execute(Booking $booking, User $user): Booking
    {
        if ($booking->owner_id !== $user->id) {
            throw new UnauthorizedBookingActionException(
                'only the asset owner can confirm that the booking is complated'
            );
        }

        if ($booking->booking_status !== BookingStatusEnum::IN_PROGRESS) {
            throw new InvalidStateTransitionException(
                'only booking in progress are chnaged to completed'
            );
        }

        $booking->update([
            'booking_status' => BookingStatusEnum::COMPLETED,
            'actual_end_datetime' => now(),
        ]);

        return $booking->refresh();
    }
}
