<?php
namespace App\Domains\Shared\Enums\EscrowLedger;
use App\Domains\Shared\Traits\EnumValues;
 
enum EscrowLedgerStatus: string{
    use EnumValues;
case PENDING_PAYMENT  = 'PENDING_PAYMENT';
case FUNDED  = 'FUNDED';
case FUNDS_HELD  = 'FUNDS_HELD';
case RELEASED  = 'RELEASED';
case PARTIAL_RELEASE  = 'PARTIAL_RELEASE';
case DISPUTE_HOLD  = 'DISPUTE_HOLD';
}