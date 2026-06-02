<?php
namespace App\Shared\Enums\EscrowLedger;
 
enum EscrowLedgerStatus: string{
case PENDING_PAYMENT  = 'PENDING_PAYMENT';
case FUNDED  = 'FUNDED';
case FUNDS_HELD  = 'FUNDS_HELD';
case RELEASED  = 'RELEASED';
case PARTIAL_RELEASE  = 'PARTIAL_RELEASE';
case DISPUTE_HOLD  = 'DISPUTE_HOLD';
}