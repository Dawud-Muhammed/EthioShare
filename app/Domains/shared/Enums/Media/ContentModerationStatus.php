<?php
namespace App\Domains\Shared\Enums\Media;
use App\Domains\Shared\Traits\EnumValues;

enum ContentModerationStatus: string{
    use EnumValues;
case PENDING = 'PENDING';
case APPROVED = 'APPROVED';
case REJECTED = 'REJECTED';
}