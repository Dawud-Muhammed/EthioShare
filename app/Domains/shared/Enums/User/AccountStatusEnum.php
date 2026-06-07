<?php 
namespace App\Domains\Shared\Enums\User;
use App\Domains\Shared\Traits\EnumValues;
enum AccountStatusEnum: string{
    use EnumValues;
    case ACTIVE = 'ACTIVE'; 
    case SUSPENDED = 'SUSPENDED';
    case BANNED = 'BANNED';
    case PENDING_VERIFICATION = 'PENDING_VERIFICATION';
}