<?php 
namespace App\Shared\Enums\Booking;
enum BookingStatusEnum: string{
case PENDING = 'PENDING';
case CONFIRMED = 'CONFIRMED';
case RENTER_ARRIVED = 'RENTER_ARRIVED';
case IN_PROGRESS = 'IN_PROGRESS';
case COMPLETED = 'COMPLETED';
case CANCELLED = 'CANCELLED';
}