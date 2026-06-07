<?php
namespace App\Domains\Shared\Enums\Media;
use App\Domains\Shared\Traits\EnumValues;

enum MediaType: string{
    use EnumValues;
case PHOTO = 'PHOTO';
case VIDEO = 'VIDEO';
case DOCUMENT = 'DOCUMENT';
case QR_CODE = 'QR_CODE';
case THERMAL_IMAGE = 'THERMAL_IMAGE';
}