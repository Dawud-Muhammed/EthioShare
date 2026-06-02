<?php 
namespace App\Shared\Enums\Asset;
enum AssetConditionEnum: string{
case NEW = 'NEW';
case LIKE_NEW = 'LIKE_NEW';
case GOOD = 'GOOD';
case FAIR = 'FAIR';
case NEEDS_REPAIR = 'NEEDS_REPAIR';
}