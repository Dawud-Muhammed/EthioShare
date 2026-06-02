<?php
namespace App\Shared\Enums\Asset;

enum DeliveryMethodEnum: string{
    case SELF_TRANSPORT = 'SELF_TRANSPORT';
    case PLATFORM_TRANSPORT = 'PLATFORM_TRANSPORT';
    case BUYER_PICKUP = 'BUYER_PICKUP';
}