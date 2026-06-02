<?php 
namespace App\Shared\Enums\Booking;
enum HandoffMethodEnum: string{
case QR_CODE = 'QR_CODE';
case MANUAL_KEY = 'MANUAL_KEY';
case DIGITAL_ACCESS = 'DIGITAL_ACCESS';
case LOCATION_PICKUP = 'LOCATION_PICKUP';
}