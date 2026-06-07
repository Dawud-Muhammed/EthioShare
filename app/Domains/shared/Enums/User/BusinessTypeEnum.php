<?php 
namespace App\Domains\Shared\Enums\User;
use App\Domains\Shared\Traits\EnumValues;
enum BusinessTypeEnum: string{
    use EnumValues;
    case INDIVIDUAL = 'INDIVIDUAL';
    case SME = 'SME';
    case CORPORATIVE = 'CORPORATIVE';
    case COOPERATIVE = 'COOPERATIVE';

}