<?php

namespace App\Listners;

use App\Domains\Bookings\Events\BookingPaymentConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingConfirmationEmail implements ShouldQueue
{
    public function handle(BookingPaymentConfirmed $event) {}
}
