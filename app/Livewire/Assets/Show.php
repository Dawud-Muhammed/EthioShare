<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Domains\Assets\Actions\PublishAssetAction;
use App\Domains\Assets\Actions\UpdateAssetStatusAction;
use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Models\Asset;
use Illuminate\View\View;
use Livewire\Component;

class Show extends Component
{
    public string $delistReason = '';

    public Asset $asset;

    public int $activePhotoIndex = 0;

    public bool $isOwner = false;

    public function mount(Asset $asset): void
    {
        // Visibility guard — same logic as AssetController@show.
        // DRAFT assets only visible to their owner.
        if ($asset->status === StatusEnum::DRAFT) {
            if (! auth()->check() || auth()->id() !== $asset->owner_id) {
                abort(404);
                // ↑ 404 not 403. Reveals nothing about the asset existing.
            }
        }

        // Eager load everything needed for the detail page.
        $asset->loadMissing([
            'media' => fn ($q) => $q
                ->where('purpose', MediaPurpose::ASSET_PHOTO)
                ->orderBy('is_primary', 'desc')
                ->orderBy('created_at', 'asc'),
            'owner:id,first_name,last_name,total_trust_score',
            // ↑ Load owner's trust score for display.
            // Renters want to know: is this owner trustworthy?
        ]);

        $this->asset = $asset;
        $this->isOwner =
        auth()->check() &&
        auth()->id() === $asset->owner_id;
    }

    public function setActivePhoto(int $index): void
    {
        $this->activePhotoIndex = $index;
        // ↑ Called when renter clicks a thumbnail.
        // Updates $activePhotoIndex → Livewire re-renders.
        // Main photo changes to the clicked thumbnail. No JavaScript needed.
        // This is Livewire replacing what would normally be Alpine.js or jQuery.
    }

    // Add this property

    // Add these methods

    public function publishAsset(): void
    {
        $action = app(PublishAssetAction::class);

        $action->execute(
            asset: $this->asset,
            owner: auth()->user(),
        );

        // Refresh the asset model to show new status immediately
        $this->asset = $this->asset->fresh();

        session()->flash('success', 'Asset published successfully.');
    }

    public function updateStatus(string $newStatus): void
    {
        $action = app(UpdateAssetStatusAction::class);

        $action->execute(
            newStatus: $newStatus,
            asset: $this->asset,
            owner: auth()->user(),
        );

        $this->asset = $this->asset->fresh();
        // ↑ fresh() reloads from database.
        // Status badge and buttons update immediately without page reload.
        // Owner sees ACTIVE change to PAUSED instantly.
    }

    public function delistAsset(): void
    {
        $this->validate([
            'delistReason' => ['required', 'string', 'min:10', 'max:500'],
            // ↑ min:10 forces a meaningful reason.
            // "ok" or "no" is not acceptable for a permanent action.
        ]);

        $action = app(UpdateAssetStatusAction::class);

        $action->execute(
            newStatus: 'DELISTED',
            asset: $this->asset,
            owner: auth()->user(),
            reason: $this->delistReason,
        );

        session()->flash('success', 'Asset delisted successfully.');
        $this->redirect(route('assets.index'), navigate: true);
    }

    public function pause(): void
    {
        $action = app(UpdateAssetStatusAction::class);

        $action->execute(
            newStatus: 'PAUSED',
            asset: $this->asset,
            owner: auth()->user(),
            reason: null,
        );

        // Refresh the asset after status change
        $this->asset = $this->asset->fresh();
        // ↑ fresh() reloads from database.
        // Page re-renders with new status badge immediately.
        // No redirect needed for pause — owner stays on detail page.
    }

    public function resume(): void
    {
        $action = app(UpdateAssetStatusAction::class);

        $action->execute(
            newStatus: 'ACTIVE',
            asset: $this->asset,
            owner: auth()->user(),
            reason: null,
        );

        $this->asset = $this->asset->fresh();
    }

    public function render(): View
    {
        return view('livewire.assets.show')
            ->layout('layouts.marketplace');
        // ↑ Public page → marketplace layout.
        // Even if the owner is viewing their own asset detail,
        // it uses the marketplace layout — consistent experience.
    }
}
