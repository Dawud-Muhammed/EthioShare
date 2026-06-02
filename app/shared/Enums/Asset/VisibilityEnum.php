<?php 
namespace App\Shared\Enums\Asset;
enum AssetVisibilityEnum: string{
   case PUBLIC = 'PUBLIC';
   case PRIVATE = 'PRIVATE';
   case REGION_RESTRICTED = 'REGION_RESTRICTED';
   case PARTNER_ONLY = 'PARTNER_ONLY';
}