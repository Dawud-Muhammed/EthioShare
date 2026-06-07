<?php
namespace  App\Domains\Shared\Enums\TrustScore;
use App\Domains\Shared\Traits\EnumValues;
enum TrustTier: string{
    use EnumValues;
case UNVERIFIED = 'UNVERIFIED';
case BRONZE = 'BRONZE';
case SILVER = 'SILVER';
case GOLD = 'GOLD';
case PLATINUM = 'PLATINUM';    
}