<?php

declare(strict_types=1);

namespace App\Domains\Bookings\Actions;

use App\Domains\Bookings\Exceptions\InvalidStateTransitionException;
use App\Domains\Bookings\Exceptions\UnauthorizedBookingActionException;
use App\Domains\Shared\Enums\Booking\BookingStatusEnum;
use App\Models\Booking;
use App\Models\User;

class ConfirmBookingAction
{
    public function execute(Booking $booking, User $owner): Booking
    {
        if ($booking->owner_id !== $owner->id) {
            throw new UnauthorizedBookingActionException(
                'Only the asset owner can confirm a booking.'
            );
        }

        if ($booking->booking_status !== BookingStatusEnum::PENDING) {
            throw new InvalidStateTransitionException(
                'Cannot confirm a booking with status: '.$booking->booking_status->value
            );
        }

        $booking->update([
            'booking_status' => BookingStatusEnum::CONFIRMED,
        ]);
        // TODO: Phase 2 — dispatch BookingConfirmed event here
        // event(new BookingConfirmed($booking));
        // This would trigger:
        // - notify renter their booking was approved
        // - prompt renter to complete payment / fund escrow
        // - set a reminder job for handoff time

        return $booking->refresh();
    }
}
