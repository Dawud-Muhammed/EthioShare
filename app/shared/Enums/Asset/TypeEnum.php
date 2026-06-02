<?php
namespace App\Shared\Enums\Asset;
enum AssetTypeEnum: string{
    case MACHINERY   = 'MACHINERY';
    case VEHICLE   = 'VEHICLE';
    case EQUIPMENT   = 'EQUIPMENT';
    case TOOLS   = 'TOOLS';
    case AGRICULTURAL   = 'AGRICULTURAL';
    case CONSTRUCTION   = 'CONSTRUCTION';
    case REAL_ESTATE   = 'REAL_ESTATE';
}