<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Domains\Assets\Actions\CreateAssetAction;
use App\Domains\Assets\Actions\PublishAssetAction;
use App\Domains\Assets\Actions\StoreAssetMediaAction;
use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;
use App\Domains\Shared\Enums\Asset\TypeEnum;
use App\Models\Asset;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    // =========================================================
    // STEP MANAGEMENT
    // =========================================================

    public int $currentStep = 1;

    public int $totalSteps = 3;
    // ↑ Public properties in Livewire are reactive.
    // When $currentStep changes, Livewire automatically re-renders
    // the component with the new value.
    // The blade view reads $currentStep to decide which step to show.

    // =========================================================
    // STEP 1 PROPERTIES — Asset Details
    // =========================================================

    public string $title = '';

    public string $description = '';

    public string $asset_type = '';

    public string $condition = '';

    public string $region = '';

    public string $address_line = '';

    public string $hourly_rate = '';

    public string $daily_rate = '';

    public string $weekly_rate = '';

    public string $monthly_rate = '';

    public string $security_deposit = '';

    public string $estimated_value = '';

    public string $delivery_method = '';

    public string $service_radius_km = '50';
    // ↑ Default 50 matches your migration default.
    // String type because HTML inputs always return strings.
    // The action handles numeric conversion automatically.

    public string $available_from = '';

    public string $available_until = '';

    // =========================================================
    // STEP 2 PROPERTIES — Photos
    // =========================================================

    public array $photos = [];
    // ↑ WithFileUploads makes this property receive UploadedFile objects.
    // Livewire stores them as temporary files until you call ->store().

    public int $primaryIndex = 0;
    // ↑ Which photo is the cover. 0 = first photo.

    // =========================================================
    // STEP 3 PROPERTIES — holds created asset for review
    // =========================================================

    public ?Asset $createdAsset = null;
    // ↑ After Step 1 saves the asset, we store the model here.
    // Step 3 reads from this to show the review summary.
    // ?Asset means nullable — null until Step 1 completes.

    // =========================================================
    // COMPUTED PROPERTIES — for dropdowns
    // =========================================================

    public function getAssetTypesProperty(): array
    {
        return TypeEnum::values();
        // ↑ Returns ['MACHINERY', 'VEHICLE', 'EQUIPMENT', ...]
        // Used in Step 1 dropdown.
        // Livewire magic: $this->assetTypes in PHP,
        // $assetTypes in the blade view automatically.
    }

    public function getConditionsProperty(): array
    {
        return ConditionEnum::values();
    }

    public function getDeliveryMethodsProperty(): array
    {
        return DeliveryMethodEnum::values();
    }

    public function getEthiopianRegionsProperty(): array
    {
        // ↑ Hardcoded list of Ethiopian administrative regions.
        // TODO: Phase 2 — move to a config file or database table.
        return [
            'Addis Ababa',
            'Afar',
            'Amhara',
            'Benishangul-Gumuz',
            'Dire Dawa',
            'Gambela',
            'Harari',
            'Oromia',
            'Sidama',
            'Somali',
            'South Ethiopia',
            'Southwest Ethiopia',
            'Tigray',
            'Central Ethiopia',
        ];
    }

    // =========================================================
    // STEP VALIDATION
    // Each step only validates its own fields.
    // This prevents Step 2 errors blocking Step 1 navigation.
    // =========================================================

    protected function step1Rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'asset_type' => ['required', 'string', 'in:'.implode(',', TypeEnum::values())],
            'condition' => ['required', 'string', 'in:'.implode(',', ConditionEnum::values())],
            'region' => ['required', 'string'],
            'address_line' => ['required', 'string', 'max:255'],
            'hourly_rate' => ['required', 'numeric', 'min:0'],
            'daily_rate' => ['required', 'numeric', 'min:0'],
            'security_deposit' => ['required', 'numeric', 'min:0'],
            'estimated_value' => ['required', 'numeric', 'min:0'],
            'weekly_rate' => ['nullable', 'numeric', 'min:0'],
            'monthly_rate' => ['nullable', 'numeric', 'min:0'],
            'available_from' => ['nullable', 'date'],
            'available_until' => ['nullable', 'date', 'after:available_from'],
            'delivery_method' => ['nullable', 'string', 'in:'.implode(',', DeliveryMethodEnum::values())],
            'service_radius_km' => ['nullable', 'numeric', 'min:1', 'max:500'],
        ];
    }

    protected function step2Rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ];
    }

    // =========================================================
    // NAVIGATION METHODS
    // =========================================================

    public function nextStep(): void
    {
        // ↑ Called when owner clicks "Next" button.
        // Validates current step before advancing.

        if ($this->currentStep === 1) {
            $this->validate($this->step1Rules());
            // ↑ $this->validate() runs the rules and automatically
            // attaches error messages to each field.
            // If validation fails → Livewire stops here, re-renders
            // with error messages shown. nextStep() does not continue.
            // If validation passes → execution continues below.

            $this->saveStep1();
            // ↑ Save asset to database before moving to Step 2.
            // Why save now instead of at the end?
            // Because Step 2 needs the asset ID to attach photos to it.
            // Media records need mediable_id = asset.id.
        }

        if ($this->currentStep === 2) {
            $this->validate($this->step2Rules());
            $this->saveStep2();
        }

        $this->currentStep++;
        // ↑ Only reached if validation passed.
        // Livewire detects $currentStep changed → re-renders component.
        // New step UI appears. No page reload.
    }

    public function previousStep(): void
    {
        // ↑ Called when owner clicks "Back" button.
        // No validation needed going backwards.
        $this->currentStep--;
        // ↑ All form data is still in Livewire properties.
        // Step 1 fields still have their values when owner goes back.
    }

    // =========================================================
    // SAVE METHODS
    // =========================================================

    private function saveStep1(): void
    {
        if ($this->createdAsset !== null) {
            return;
            // ↑ Guard: if owner goes back to Step 1 and clicks Next again,
            // don't create a second asset record.
            // If asset already exists, skip creation silently.
        }

        $action = app(CreateAssetAction::class);
        // ↑ app() is Laravel's service container helper.
        // It creates an instance of CreateAssetAction with all its
        // dependencies injected automatically.
        // Same as constructor injection but used inline.

        $this->createdAsset = $action->execute(
            data: [
                'title' => $this->title,
                'description' => $this->description ?: null,
                'asset_type' => $this->asset_type,
                'condition' => $this->condition,
                'region' => $this->region,
                'address_line' => $this->address_line,
                'hourly_rate' => $this->hourly_rate,
                'daily_rate' => $this->daily_rate,
                'weekly_rate' => $this->weekly_rate ?: null,
                'monthly_rate' => $this->monthly_rate ?: null,
                'security_deposit' => $this->security_deposit,
                'estimated_value' => $this->estimated_value,
                'available_from' => $this->available_from ?: null,
                'available_until' => $this->available_until ?: null,
                'delivery_method' => $this->delivery_method ?: null,
                'service_radius_km' => $this->service_radius_km ?: 50,
            ],
            owner: auth()->user(),
            // ↑ auth()->user() returns the logged-in User model.
            // No token needed. Session handles authentication on the web.
        );
    }

    private function saveStep2(): void
    {
        $action = app(StoreAssetMediaAction::class);

        $action->execute(
            files: $this->photos,
            asset: $this->createdAsset,
            owner: auth()->user(),
            primaryIndex: $this->primaryIndex,
        );
    }

    public function removePhoto(int $index): void
    {
        // Remove photo at given index from the array
        array_splice($this->photos, $index, 1);
        // ↑ array_splice removes element at $index and re-indexes the array.
        // After removal, if removed photo was primary, reset to first photo.

        // If removed photo was primary or primary index now out of bounds
        if ($this->primaryIndex >= count($this->photos)) {
            $this->primaryIndex = 0;
            // ↑ Reset to first photo if primary was deleted or index invalid.
        }
    }

    public function setPrimary(int $index): void
    {
        $this->primaryIndex = $index;
        // ↑ Owner clicks a thumbnail to make it the cover photo.
        // Simple property update. Livewire re-renders immediately.
    }

    // =========================================================
    // FINAL ACTIONS — Step 3 buttons
    // =========================================================

    public function publish(): void
    {
        // ↑ Owner clicks "Publish Now" on Step 3.
        $action = app(PublishAssetAction::class);

        $action->execute(
            asset: $this->createdAsset,
            owner: auth()->user(),
        );

        session()->flash('success', 'Asset published successfully!');
        // ↑ Flash message survives ONE redirect.
        // After redirect, MyAssets page reads this and shows a toast.

        $this->redirect(route('assets.index'), navigate: true);
        // ↑ navigate: true → uses Livewire's wire:navigate system.
        // SPA-like redirect. No full page reload.
    }

    public function saveDraft(): void
    {
        // ↑ Owner clicks "Save as Draft" on Step 3.
        // Asset is already saved from Step 1. Nothing more to do.
        // Just redirect to MyAssets.

        session()->flash('success', 'Asset saved as draft.');

        $this->redirect(route('assets.index'), navigate: true);
    }

    // =========================================================
    // RENDER
    // =========================================================

    public function render(): View
    {
        return view('livewire.assets.create', [
            'assetTypes' => TypeEnum::values(),
            'conditions' => ConditionEnum::values(),
            'deliveryMethods' => DeliveryMethodEnum::values(),
            'ethiopianRegions' => $this->ethiopianRegions,
        ])
            ->layout('layouts.app');
    }
}
