<?php
namespace App\Domains\Shared\Enums\Media;
use App\Domains\Shared\Traits\EnumValues;

enum VirusScanStatus: string{
    use EnumValues;
case PENDING = 'PENDING'; 
case PASSED = 'PASSED'; 
case FLAGGED = 'FLAGGED';
}