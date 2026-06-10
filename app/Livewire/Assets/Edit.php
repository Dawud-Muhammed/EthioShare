<?php

declare(strict_types=1);

namespace App\Livewire\Assets;

use App\Domains\Assets\Actions\StoreAssetMediaAction;
use App\Domains\Assets\Actions\UpdateAssetAction;
use App\Domains\Shared\Enums\Asset\TypeEnum;
use App\Domains\Shared\Enums\Asset\ConditionEnum;
use App\Domains\Shared\Enums\Asset\DeliveryMethodEnum;
use App\Domains\Shared\Enums\Media\MediaPurpose;
use App\Models\Asset;
use App\Models\Media;
use Livewire\Component;
use Livewire\WithFileUploads;


class Edit extends Component
{
    use WithFileUploads;
    public array $newPhotos = [];
    public Asset $asset;
    // ↑ Livewire supports model binding via route parameters.
    // When the route has {asset}, Livewire automatically finds
    // the Asset model and injects it here.
    // This is called Livewire Route Model Binding.
    // It works because the property type is Asset.

    public string $title        = '';
    public string $description  = '';
    public string $asset_type   = '';
    public string $condition    = '';
    public string $region       = '';
    public string $address_line = '';
    public string $hourly_rate      = '';
    public string $daily_rate       = '';
    public string $weekly_rate      = '';
    public string $monthly_rate     = '';
    public string $security_deposit = '';
    public string $estimated_value  = '';
    public string $delivery_method   = '';
    public string $service_radius_km = '';
    public string $available_from  = '';
    public string $available_until = '';

    public function mount(Asset $asset): void
    {
        // ↑ mount() is Livewire's initialization method.
        // It runs ONCE when the component first loads.
        // Like a constructor for Livewire components.
        // We use it to pre-fill the form with existing asset data.

        // Replace $this->authorize('update', $asset) with this:
    if (auth()->id() !== $asset->owner_id) {
        abort(403, 'You do not own this asset.');
    }
        // ↑ Checks if logged-in user can update this asset.
        // TODO: create AssetPolicy for this.
        // For now replace with manual check:
        // if (auth()->id() !== $asset->owner_id) abort(403);

        $this->asset = $asset;

        // Pre-fill all form properties from the existing asset.
        $this->title           = $asset->title;
        $this->description     = $asset->description ?? '';
        $this->asset_type      = $asset->asset_type->value ?? $asset->asset_type;
        $this->condition       = $asset->condition->value ?? $asset->condition;
        $this->region          = $asset->region;
        $this->address_line    = $asset->address_line ?? '';
        $this->hourly_rate     = (string) $asset->hourly_rate;
        $this->daily_rate      = (string) $asset->daily_rate;
        $this->weekly_rate     = (string) ($asset->weekly_rate ?? '');
        $this->monthly_rate    = (string) ($asset->monthly_rate ?? '');
        $this->security_deposit = (string) $asset->security_deposit;
        $this->estimated_value  = (string) $asset->estimated_value;
        $this->delivery_method = $asset->delivery_method?->value ?? '';
        $this->service_radius_km = (string) ($asset->service_radius_km ?? '50');
        $this->available_from  = $asset->available_from?->format('Y-m-d') ?? '';
        $this->available_until = $asset->available_until?->format('Y-m-d') ?? '';
        // ↑ ?->format() nullsafe operator.
        // If available_from is null, returns '' instead of crashing.
        // format('Y-m-d') gives "2026-06-01" — correct format for type="date" inputs.
    }

    protected function rules(): array
    {
        return [
            'title'            => ['required', 'string', 'min:5', 'max:255'],
            'description'      => ['nullable', 'string', 'max:5000'],
            'asset_type'       => ['required', 'string', 'in:' . implode(',', TypeEnum::values())],
            'condition'        => ['required', 'string', 'in:' . implode(',', ConditionEnum::values())],
            'region'           => ['required', 'string'],
            'address_line'     => ['required', 'string', 'max:255'],
            'hourly_rate'      => ['required', 'numeric', 'min:0'],
            'daily_rate'       => ['required', 'numeric', 'min:0'],
            'weekly_rate'      => ['nullable', 'numeric', 'min:0'],
            'monthly_rate'     => ['nullable', 'numeric', 'min:0'],
            'security_deposit' => ['required', 'numeric', 'min:0'],
            'estimated_value'  => ['required', 'numeric', 'min:0'],
            'delivery_method'  => ['nullable', 'string', 'in:' . implode(',', DeliveryMethodEnum::values())],
            'service_radius_km'=> ['nullable', 'numeric', 'min:1', 'max:500'],
            'available_from'   => ['nullable', 'date'],
            'available_until'  => ['nullable', 'date', 'after:available_from'],
        ];
    }

    public function save(): void
    {
        $this->validate();
        // ↑ No argument = uses $this->rules() automatically.

        $action = app(UpdateAssetAction::class);

        $action->execute(
            data: [
                'title'             => $this->title,
                'description'       => $this->description ?: null,
                'asset_type'        => $this->asset_type,
                'condition'         => $this->condition,
                'region'            => $this->region,
                'address_line'      => $this->address_line,
                'hourly_rate'       => $this->hourly_rate,
                'daily_rate'        => $this->daily_rate,
                'weekly_rate'       => $this->weekly_rate ?: null,
                'monthly_rate'      => $this->monthly_rate ?: null,
                'security_deposit'  => $this->security_deposit,
                'estimated_value'   => $this->estimated_value,
                'delivery_method'   => $this->delivery_method ?: null,
                'service_radius_km' => $this->service_radius_km ?: 50,
                'available_from'    => $this->available_from ?: null,
                'available_until'   => $this->available_until ?: null,
            ],
            asset: $this->asset,
            owner: auth()->user(),
            newPhotos: $this->newPhotos,
        );

        $this->newPhotos = [];
        session()->flash('success', 'Asset updated successfully.');
        $this->redirect(route('assets.index'), navigate: true);
    }

    public function getAssetTypesProperty(): array { return TypeEnum::values(); }
    public function getConditionsProperty(): array { return ConditionEnum::values(); }
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

    public function getExistingPhotosProperty()
    {
        return $this->asset->media()
            ->where('purpose', MediaPurpose::ASSET_PHOTO)
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function deletePhoto(string $mediaId): void
    {
        $media = Media::find($mediaId);

        if (!$media || $media->mediable_id !== $this->asset->id) {
            return;
        }

        // Delete file from disk
        \Illuminate\Support\Facades\Storage::disk('public')
            ->delete($media->disk_path);

        // Delete the database record
        $media->delete();

        // TODO: Phase 2 — if deleted photo was primary, promote next photo automatically.
    }

    public function setExistingPrimary(string $mediaId): void
    {
        // First demote all existing primary photos
        $this->asset->media()
            ->where('purpose', MediaPurpose::ASSET_PHOTO)
            ->where('is_primary', true)
            ->update(['is_primary' => false]);

        // Then promote the selected one
        Media::where('id', $mediaId)
            ->where('mediable_id', $this->asset->id)
            ->update(['is_primary' => true]);
    }

    public function uploadNewPhotos(): void
    {
        $this->validate([
            'newPhotos'   => ['required', 'array', 'min:1', 'max:10'],
            'newPhotos.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
        ]);

        $action = app(StoreAssetMediaAction::class);

        $action->execute(
            files:        $this->newPhotos,
            asset:        $this->asset,
            owner:        auth()->user(),
            primaryIndex: -1,
            // ↑ -1 means "don't set any new photo as primary".
            // Existing primary stays primary unless owner explicitly changes it.
            // TODO: update StoreAssetMediaAction to handle -1 as "no primary change".
        );

        $this->newPhotos = [];
        // ↑ Clear the upload input after successful upload.
        // Owner sees the new photos appear in existing photos section immediately.
    }
    public function render(): \Illuminate\View\View
    {
        return view('livewire.assets.edit', [
            'assetTypes' => TypeEnum::values(),
            'conditions' => ConditionEnum::values(),
            'deliveryMethods' => DeliveryMethodEnum::values(),
            'existingPhotos' => $this->existingPhotos,
            'ethiopianRegions' => $this->ethiopianRegions,
        ])
            ->layout('layouts.app');
    }
}