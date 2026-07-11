<?php

declare(strict_types=1);

namespace App\Domains\Assets\Actions;

use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class PublishAssetAction
{
    public function execute(Asset $asset, User $owner): Asset
    {
        // ↑ Takes the asset to publish and the owner requesting it.

        $this->ensureOwnership($asset, $owner);
        $this->ensureCorrectStatus($asset);
        $this->ensureHasPhotos($asset);
        $this->ensureRequiredFieldsExist($asset);

        return DB::transaction(function () use ($asset) {
            $asset->update([
                'status' => 'ACTIVE',
                'status_updated_at' => now(),
                // Phase 2 uses this for analytics: how long do assets stay in DRAFT?
            ]);
            // TODO: Phase 2 — dispatch AssetPublished event here:
            // event(new \App\Domains\Assets\Events\AssetPublished($asset, $owner));
            // Listeners will: update search index, notify admin, send owner confirmation SMS.

            return $asset->fresh();
        });

    }

    private function ensureOwnership(Asset $asset, User $owner): void
    {
        if ($asset->owner_id !== $owner->id) {
            throw new AuthorizationException(
                'You do not own this asset'
            );
        }
    }

    private function ensureCorrectStatus(Asset $asset): void
    {
        if ($asset->status !== StatusEnum::DRAFT) {
            throw new \InvalidArgumentException(
                "Only DRAFT assets can be published. Current status: {$asset->status->value}"
            );
        }
    }

    private function ensureHasPhotos(Asset $asset): void
    {
        $photoCount = $asset->media()
            ->where('purpose', MediaPurpose::ASSET_PHOTO)
            ->count();

        if ($photoCount === 0) {
            throw new \InvalidArgumentException(
                'Asset must have at least one photo before publishing.'
            );
        }
    }

    private function ensureRequiredFieldsExist(Asset $asset): void
    {
        $missing = [];

        if (empty($asset->hourly_rate)) {
            $missing[] = 'hourly_rate';
        }
        if (empty($asset->daily_rate)) {
            $missing[] = 'daily_rate';
        }
        if (empty($asset->region)) {
            $missing[] = 'region';
        }
        if (empty($asset->address_line)) {
            $missing[] = 'address_line';
        }
        if (empty($asset->security_deposit)) {
            $missing[] = 'security_deposit';
        }

        if (! empty($missing)) {
            throw new \InvalidArgumentException(
                'Asset is missing required fields: '.implode(', ', $missing)
            );
        }
    }
}
