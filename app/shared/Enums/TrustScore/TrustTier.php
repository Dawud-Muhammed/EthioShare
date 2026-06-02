<?php
namespace App\Shared\Enums\TrustScore;

enum TrustTier: string{
case UNVERIFIED = 'UNVERIFIED';
case BRONZE = 'BRONZE';
case SILVER = 'SILVER';
case GOLD = 'GOLD';
case PLATINUM = 'PLATINUM';    
}