<?php

namespace App\Domains\Shared\Enums\Asset;

use App\Domains\Shared\Traits\EnumValues;

enum TypeEnum: string
{
    use EnumValues;
    case MACHINERY = 'MACHINERY';
    case VEHICLE = 'VEHICLE';
    case EQUIPMENT = 'EQUIPMENT';
    case TOOLS = 'TOOLS';
    case AGRICULTURAL = 'AGRICULTURAL';
    case CONSTRUCTION = 'CONSTRUCTION';
    case REAL_ESTATE = 'REAL_ESTATE';
}
