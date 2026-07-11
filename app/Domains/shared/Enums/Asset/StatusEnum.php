<?php

namespace App\Domains\Shared\Enums\Asset;

use App\Domains\Shared\Traits\EnumValues;

enum StatusEnum: string
{
    use EnumValues;
    case DRAFT = 'DRAFT';
    case ACTIVE = 'ACTIVE';
    case RENTED = 'RENTED';
    case PAUSED = 'PAUSED';
    case DELISTED = 'DELISTED';
    case ARCHIVED = 'ARCHIVED';
}
