<?php
namespace App\Shared\Enums\Dispute;
enum StatusEnum: string{
case OPEN = 'OPEN';
case UNDER_REVIEW = 'UNDER_REVIEW';
case AWAITING_RESPONSE = 'AWAITING_RESPONSE';
case MEDIATED = 'MEDIATED';
case RESOLVED = 'RESOLVED';    
case ESCALATED = 'ESCALATED';  
}