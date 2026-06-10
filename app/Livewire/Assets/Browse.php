<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;
use App\Domains\Shared\Enums\Asset\StatusEnum;
use App\Domains\Shared\Enums\Asset\TypeEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;

class Browse extends Component
{
    use WithPagination;

    // =========================================================
    // FILTER PROPERTIES
    // Each one maps directly to a filter in your index endpoint logic.
    // Public = reactive. Change any of these → list re-renders.
    // =========================================================

    public string $region          = '';
    public string $assetType       = '';
    public string $condition       = '';
    public string $deliveryMethod  = '';
    public string $minPrice        = '';
    public string $maxPrice        = '';
    public string $search          = '';
    // ↑ Free text search on title. Phase 1 addition.
    // TODO: Phase 2 — replace with full-text search via Scout + Meilisearch.

    // =========================================================
    // LIVEWIRE LIFECYCLE HOOKS
    // =========================================================

    public function updatedRegion(): void         { $this->resetPage(); }
    public function updatedAssetType(): void      { $this->resetPage(); }
    public function updatedCondition(): void      { $this->resetPage(); }
    public function updatedDeliveryMethod(): void { $this->resetPage(); }
    public function updatedMinPrice(): void       { $this->resetPage(); }
    public function updatedMaxPrice(): void       { $this->resetPage(); }
    public function updatedSearch(): void         { $this->resetPage(); }
    // ↑ Every filter change resets to page 1.
    // Same reasoning as Index component.
    // Each updatedXxx() matches a property name exactly.
    // Livewire calls the right one automatically when that property changes.

    public function clearFilters(): void
    {
        // ↑ Called when owner clicks "Clear All Filters" button.
        $this->reset([
            'region', 'assetType', 'condition',
            'deliveryMethod', 'minPrice', 'maxPrice', 'search',
        ]);
        // ↑ $this->reset() resets listed properties to their default values.
        // All strings go back to '' simultaneously.
        // Cleaner than setting each one to '' manually.
        $this->resetPage();
    }

    public function getAssetTypesProperty(): array   { return TypeEnum::values(); }
    public function getConditionsProperty(): array    { return ConditionEnum::values(); }
    public function getDeliveryMethodsProperty(): array { return DeliveryMethodEnum::values(); }
    public function getEthiopianRegionsProperty(): array
    {
        return [
            'Addis Ababa', 'Afar', 'Amhara', 'Benishangul-Gumuz',
            'Dire Dawa', 'Gambela', 'Harari', 'Oromia', 'Sidama',
            'Somali', 'South Ethiopia', 'Southwest Ethiopia',
            'Tigray', 'Central Ethiopia',
        ];
    }

    public function render(): \Illuminate\View\View
    {
        $assets = Asset::query()
            ->where('status', StatusEnum::ACTIVE)
            // ↑ ONLY active assets. Same rule as your API index endpoint.
            // Public browse never shows DRAFT, PAUSED, or DELISTED assets.

            ->when($this->search !== '', function ($q) {
                $q->where('title', 'ilike', '%' . $this->search . '%');
                // ↑ 'ilike' is PostgreSQL's case-insensitive LIKE.
                // 'excavator' matches 'Excavator', 'EXCAVATOR', 'excavator'.
                // Standard MySQL uses 'like' (case-insensitive by default).
                // Since you use PostgreSQL, 'ilike' is correct here.
                // '%' before and after = match anywhere in the title.
            })

            ->when($this->region !== '', function ($q) {
                $q->where('region', $this->region);
            })

            ->when($this->assetType !== '', function ($q) {
                $q->where('asset_type', $this->assetType);
            })

            ->when($this->condition !== '', function ($q) {
                $q->where('condition', $this->condition);
            })

            ->when($this->deliveryMethod !== '', function ($q) {
                $q->where('delivery_method', $this->deliveryMethod);
            })

            ->when($this->minPrice !== '', function ($q) {
                $q->where('daily_rate', '>=', (float) $this->minPrice);
                // ↑ (float) cast because $minPrice is a string from the input.
                // '100' (string) becomes 100.0 (float) for numeric comparison.
            })

            ->when($this->maxPrice !== '', function ($q) {
                $q->where('daily_rate', '<=', (float) $this->maxPrice);
            })

            ->with([
                'media' => fn($q) => $q
                    ->where('purpose', MediaPurpose::ASSET_PHOTO)
                    ->where('is_primary', true)
                    ->limit(1),
                'owner:id,first_name,last_name',
            ])
            ->withCount('media')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        // ↑ 12 per page instead of 10.
        // Why 12? Grid layouts look better with multiples of 3 or 4.
        // 12 fills a 3-column grid perfectly (4 rows).
        // 10 would leave an awkward gap on the last row.

        return view('livewire.assets.browse', [
            'assets' => $assets,
            'assetTypes' => TypeEnum::values(),           
            'conditions' => ConditionEnum::values(),
            'deliveryMethods' => DeliveryMethodEnum::values(),
            'ethiopianRegions' => $this->ethiopianRegions,
            ])->layout('layouts.marketplace');
        // ↑ Public page → marketplace layout, not app layout.
        // This is the distinction we established in the planning phase.
    }
}