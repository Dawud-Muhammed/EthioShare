<?php 
namespace App\Shared\Enums\Asset;

enum AssetStatusEnum: string{
   case DRAFT = 'DRAFT';
   case ACTIVE = 'ACTIVE';
   case RENTED = 'RENTED';
   case PAUSED = 'PAUSED';
   case DELISTED = 'DELISTED';
   case ARCHIVED = 'ARCHIVED';
}