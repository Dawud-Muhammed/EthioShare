<?php 
namespace App\Shared\Enums\Booking;
enum PaymentStatusEnum: string{
case PENDING = 'PENDING';
case AUTHORIZED = 'AUTHORIZED';
case CAPTURED = 'CAPTURED';
case REFUNDED = 'REFUNDED';
case FAILED = 'FAILED';
}