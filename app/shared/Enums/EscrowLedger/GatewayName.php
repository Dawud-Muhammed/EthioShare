<?php
namespace App\Shared\Enums\EscrowLedger;

enum GatewayName: string{
case CHAPA = 'CHAPA';
case SANTIM_PAY = 'SANTIM_PAY';
case TELEBIRR = 'TELEBIRR';
case OTHER = 'OTHER';
}