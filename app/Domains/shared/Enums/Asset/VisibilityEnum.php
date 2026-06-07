<?php 
namespace App\Domains\Shared\Enums\Asset;
use App\Domains\Shared\Traits\EnumValues;
enum VisibilityEnum: string{
   use EnumValues;
   case PUBLIC = 'PUBLIC';
   case PRIVATE = 'PRIVATE';
   case REGION_RESTRICTED = 'REGION_RESTRICTED';
   case PARTNER_ONLY = 'PARTNER_ONLY';
}