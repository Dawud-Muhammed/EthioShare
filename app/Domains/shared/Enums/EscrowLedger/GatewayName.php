<?php

namespace App\Domains\Shared\Enums\EscrowLedger;

use App\Domains\Shared\Traits\EnumValues;

enum GatewayName: string
{
    use EnumValues;
    case CHAPA = 'CHAPA';
    case SANTIM_PAY = 'SANTIM_PAY';
    case TELEBIRR = 'TELEBIRR';
    case OTHER = 'OTHER';
}
