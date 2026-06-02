<?php 
namespace App\Shared\Enums\Booking;
enum EscrowStatusEnum: string{
case PENDING = 'PENDING';
case FUNDED = 'FUNDED';
case HELD = 'HELD';
case RELEASED = 'RELEASED';
case PARTIALLY_REALISED = 'PARTIALLY_REALISED';
}