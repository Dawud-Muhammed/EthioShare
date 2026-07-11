<?php

namespace App\Domains\Shared\Enums\Booking;

use App\Domains\Shared\Traits\EnumValues;

enum EscrowStatusEnum: string
{
    use EnumValues;
    case PENDING = 'PENDING';
    case FUNDED = 'FUNDED';
    case HELD = 'HELD';
    case RELEASED = 'RELEASED';
    case PARTIALLY_REALISED = 'PARTIALLY_REALISED';
    case FAILED = 'FAILED';
}
