<?php

namespace App\Domains\Bookings\Events;

use App\Models\Booking;
use App\Models\EscrowLedger;

class BookingPaymentConfirmed
{
    public function __construct(
        public readonly Booking $booking,
        public readonly EscrowLedger $ledger,
    ) {}
}
