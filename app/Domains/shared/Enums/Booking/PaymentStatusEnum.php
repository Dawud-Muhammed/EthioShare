<?php 
namespace App\Domains\Shared\Enums\Booking;
use App\Domains\Shared\Traits\EnumValues;
enum PaymentStatusEnum: string{
    use EnumValues;
case PENDING = 'PENDING';
case AUTHORIZED = 'AUTHORIZED';
case CAPTURED = 'CAPTURED';
case REFUNDED = 'REFUNDED';
case FAILED = 'FAILED';
}