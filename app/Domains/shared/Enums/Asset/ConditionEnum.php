<?php

namespace App\Domains\Shared\Enums\Asset;

use App\Domains\Shared\Traits\EnumValues;

enum ConditionEnum: string
{
    use EnumValues;
    case NEW = 'NEW';
    case LIKE_NEW = 'LIKE_NEW';
    case GOOD = 'GOOD';
    case FAIR = 'FAIR';
    case NEEDS_REPAIR = 'NEEDS_REPAIR';
}
