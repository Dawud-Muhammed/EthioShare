<?php 
namespace App\Shared\Enums\User;
enum AccountStatusEnum: string{
    case ACTIVE = 'ACTIVE'; 
    case SUSPENDED = 'SUSPENDED';
    case BANNED = 'BANNED';
    case PENDING_VERIFICATION = 'PENDING_VERIFICATION';
}