<?php 
namespace App\Domains\Shared\Enums\Booking;
use App\Domains\Shared\Traits\EnumValues;
enum BookingStatusEnum: string{
    use EnumValues;
case PENDING = 'PENDING';
case CONFIRMED = 'CONFIRMED';
case RENTER_ARRIVED = 'RENTER_ARRIVED';
case IN_PROGRESS = 'IN_PROGRESS';
case COMPLETED = 'COMPLETED';
case CANCELLED = 'CANCELLED';
}