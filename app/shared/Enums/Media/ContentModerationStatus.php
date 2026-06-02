<?php
namespace App\Shared\Enums\Media;

enum ContentModerationStatus: string{
case PENDING = 'PENDING';
case APPROVED = 'APPROVED';
case REJECTED = 'REJECTED';
}