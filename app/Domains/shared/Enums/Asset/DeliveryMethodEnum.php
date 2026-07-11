<?php

namespace App\Domains\Shared\Enums\Asset;

use App\Domains\Shared\Traits\EnumValues;

enum DeliveryMethodEnum: string
{
    use EnumValues;
    case SELF_TRANSPORT = 'SELF_TRANSPORT';
    case PLATFORM_TRANSPORT = 'PLATFORM_TRANSPORT';
    case BUYER_PICKUP = 'BUYER_PICKUP';
}
