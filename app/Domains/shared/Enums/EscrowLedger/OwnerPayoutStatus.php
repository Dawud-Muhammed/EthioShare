<?php
namespace App\Domains\Shared\Enums\EscrowLedger;
use App\Domains\Shared\Traits\EnumValues;
enum OwnerPayoutStatus: string{
    use EnumValues;
case PENDING = 'PENDING';
case SCHEDULED = 'SCHEDULED';
case COMPLETED = 'COMPLETED';
case FAILED = 'FAILED'; 
case REVERSED = 'REVERSED';  
}