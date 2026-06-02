<?php
namespace App\Shared\Enums\EscrowLedger;

enum OwnerPayoutStatus: string{
case PENDING = 'PENDING';
case SCHEDULED = 'SCHEDULED';
case COMPLETED = 'COMPLETED';
case FAILED = 'FAILED'; 
case REVERSED = 'REVERSED';  
}