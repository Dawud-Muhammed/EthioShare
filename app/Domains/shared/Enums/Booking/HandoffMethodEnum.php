<?php

namespace App\Domains\Shared\Enums\Booking;

use App\Domains\Shared\Traits\EnumValues;

enum HandoffMethodEnum: string
{
    use EnumValues;
    case QR_CODE = 'QR_CODE';
    case MANUAL_KEY = 'MANUAL_KEY';
    case DIGITAL_ACCESS = 'DIGITAL_ACCESS';
    case LOCATION_PICKUP = 'LOCATION_PICKUP';
}
