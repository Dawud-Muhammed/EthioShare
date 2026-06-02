<?php
namespace App\Shared\Enums\Dispute;

enum ReasonEnum: string{
case ASSET_DAMAGE = 'ASSET_DAMAGE';
case MISSING_HOURS = 'MISSING_HOURS';
case LATE_RETURN = 'LATE_RETURN';
case RENTER_NO_SHOW = 'RENTER_NO_SHOW';
case OTHER = 'OTHER';    
}