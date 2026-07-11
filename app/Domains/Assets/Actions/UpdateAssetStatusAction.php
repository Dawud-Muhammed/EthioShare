<?php

declare(strict_types=1);

namespace App\Domains\Assets\Actions;

use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateAssetStatusAction
{
    private const ALLOWED_TRANSITIONS = [
        'DRAFT' => ['DELISTED'],
        'ACTIVE' => ['PAUSED', 'DELISTED'],
        'PAUSED' => ['ACTIVE', 'DELISTED'],
    ];

    public function execute(string $newStatus, Asset $asset, User $owner, ?string $reason = null): Asset
    {
        $this->ensureOwnership($asset, $owner);
        $this->ensureTransitionIsAllowed($asset, $newStatus);

        return DB::transaction(function () use ($asset, $newStatus, $reason) {
            $asset->update([
                'status' => $newStatus,
                'status->updated_at' => now(),
            ]);

            // Store the reason as an audit note when delisting.
            // TODO: Phase 2 — replace this with a proper AuditLog entry:
            // AuditLog::create([
            //     'user_id'     => $owner->id,
            //     'action_type' => 'ASSET_DELISTED',
            //     'entity_type' => 'Asset',
            //     'entity_id'   => $asset->id,
            //     'new_values'  => ['status' => $newStatus, 'reason' => $reason],
            // ]);
            // For now we store reason in a log so it is not lost:

            if ($newStatus === StatusEnum::DELISTED->value && $reason) {
                Log::info('Asset delisted', [
                    'asset_id' => $asset->id,
                    'owner_id' => $asset->owner_id,
                    'reason' => $reason,
                    'timestamp' => now(),
                ]);
                // ↑ Log::info() writes to storage/logs/laravel.log
                // Not a permanent solution — but nothing is lost.
                // Phase 2 replaces this with the AuditLog model above.
            }

            // TODO: Phase 2 — dispatch status-specific events:
            // match($newStatus) {
            //     'PAUSED'   => event(new AssetPaused($asset)),
            //     'DELISTED' => event(new AssetDelisted($asset, $reason)),
            // }
            // Listeners: notify active bookers, remove from search index.
            return $asset->fresh();
        });
    }

    private function ensureOwnership(Asset $asset, User $owner): void
    {
        if ($asset->owner_id !== $owner->id) {
            throw new AuthorizationException(
                'this asset is not yours'
            );
        }
    }

    private function ensureTransitionIsAllowed(Asset $asset, string $newStatus): void
    {
        $currentStatus = $asset->status->value;
        $allowedStatusTransition = self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];

        if (! in_array($newStatus, $allowedStatusTransition)) {
            throw new \InvalidArgumentException(
                "Cannot transition asset from {$currentStatus} to {$newStatus}. ".'Allowed Transitions: '.implode(', ', $allowedStatusTransition).(empty($allowedStatusTransition) ? 'none' : '')
            );
        }
    }
}
