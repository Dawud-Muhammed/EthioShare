<?php
namespace App\Shared\Enums\Media;

enum MediaType: string{
case PHOTO = 'PHOTO';
case VIDEO = 'VIDEO';
case DOCUMENT = 'DOCUMENT';
case QR_CODE = 'QR_CODE';
case THERMAL_IMAGE = 'THERMAL_IMAGE';
}