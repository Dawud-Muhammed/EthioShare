<?php
namespace App\Domains\Shared\Enums\Dispute;
use App\Domains\Shared\Traits\EnumValues;
enum StatusEnum: string{
    use EnumValues;
case OPEN = 'OPEN';
case UNDER_REVIEW = 'UNDER_REVIEW';
case AWAITING_RESPONSE = 'AWAITING_RESPONSE';
case MEDIATED = 'MEDIATED';
case RESOLVED = 'RESOLVED';    
case ESCALATED = 'ESCALATED';  
}