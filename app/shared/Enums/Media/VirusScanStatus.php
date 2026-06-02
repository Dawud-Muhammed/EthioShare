<?php
namespace App\Shared\Enums\Media;

enum VirusScanStatus: string{
case PENDING = 'PENDING'; 
case PASSED = 'PASSED'; 
case FLAGGED = 'FLAGGED';
}