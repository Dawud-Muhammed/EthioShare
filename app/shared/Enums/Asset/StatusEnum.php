<?php 
namespace App\Shared\Enums\Asset;

enum StatusEnum: string{
   case DRAFT = 'DRAFT';
   case ACTIVE = 'ACTIVE';
   case RENTED = 'RENTED';
   case PAUSED = 'PAUSED';
   case DELISTED = 'DELISTED';
   case ARCHIVED = 'ARCHIVED';
}