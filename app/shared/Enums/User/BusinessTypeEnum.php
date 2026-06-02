<?php 
namespace App\Shared\Enums\User;
enum BusinessTypeEnum: string{
    case INDIVIDUAL = 'INDIVIDUAL';
    case SME = 'SME';
    case CORPORATIVE = 'CORPORATIVE';
    case COOPERATIVE = 'COOPERATIVE';

}