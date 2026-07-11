<?php

namespace App\Domains\Shared\Enums\Media;

use App\Domains\Shared\Traits\EnumValues;

enum MediaPurpose: string
{
    use EnumValues;
    case KYC_VERIFICATION = 'KYC_VERIFICATION';
    case ASSET_PHOTO = 'ASSET_PHOTO';
    case ASSET_INSPECTION = 'ASSET_INSPECTION';
    case BOOKING_HANDOFF = 'BOOKING_HANDOFF';
    case DISPUTE_EVIDENCE = 'DISPUTE_EVIDENCE';
    case OTHER = 'OTHER';
}
