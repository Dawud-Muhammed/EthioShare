<div class="max-w-3xl mx-auto py-8 px-4">

    <div class="mb-8">
        <flux:heading size="xl">{{ __('Edit Asset') }}</flux:heading>
        <flux:text class="mt-1 text-zinc-500">
            {{ __('Update your asset details below.') }}
        </flux:text>
    </div>

    <div class="space-y-6">

        {{-- Basic Info --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>

            <flux:input
                wire:model="title"
                :label="__('Asset Title')"
            />
            @error('title')
                <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
            @enderror

            <flux:textarea
                wire:model="description"
                :label="__('Description')"
                rows="3"
            />

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:select wire:model="asset_type" :label="__('Asset Type')">
                        <option value="">{{ __('Select type...') }}</option>
                        @foreach ($assetTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </flux:select>
                    @error('asset_type')
                        <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>
                <div>
                    <flux:select wire:model="condition" :label="__('Condition')">
                        <option value="">{{ __('Select condition...') }}</option>
                        @foreach ($conditions as $cond)
                            <option value="{{ $cond }}">{{ $cond }}</option>
                        @endforeach
                    </flux:select>
                    @error('condition')
                        <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Pricing --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Pricing') }}</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:input wire:model="hourly_rate" :label="__('Hourly Rate (ETB)')" type="number" min="0"/>
                    @error('hourly_rate') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                </div>
                <div>
                    <flux:input wire:model="daily_rate" :label="__('Daily Rate (ETB)')" type="number" min="0"/>
                    @error('daily_rate') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                </div>
                <div>
                    <flux:input wire:model="security_deposit" :label="__('Security Deposit (ETB)')" type="number" min="0"/>
                    @error('security_deposit') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                </div>
                <div>
                    <flux:input wire:model="estimated_value" :label="__('Estimated Value (ETB)')" type="number" min="0"/>
                    @error('estimated_value') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Location') }}</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:select wire:model="region" :label="__('Region')">
                        <option value="">{{ __('Select region...') }}</option>
                        @foreach ($ethiopianRegions as $reg)
                            <option value="{{ $reg }}">{{ $reg }}</option>
                        @endforeach
                    </flux:select>
                    @error('region') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                </div>
                <div>
                    <flux:select wire:model="delivery_method" :label="__('Delivery Method')">
                        <option value="">{{ __('Select method...') }}</option>
                        @foreach ($deliveryMethods as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>
            <flux:input wire:model="address_line" :label="__('Pickup Address')"/>
            @error('address_line') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
        </div>

        {{-- Availability --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Availability') }}</flux:heading>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:input wire:model="available_from" :label="__('Available From')" type="date"/>
                </div>
                <div>
                    <flux:input wire:model="available_until" :label="__('Available Until')" type="date"/>
                    @error('available_until') <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text> @enderror
                </div>
            </div>
        </div>

{{-- ===================== EXISTING PHOTOS ===================== --}}
<div class="space-y-4">
    <flux:heading size="lg">{{ __('Photos') }}</flux:heading>

    {{-- Existing saved photos --}}
    @if ($existingPhotos->isNotEmpty())
        <flux:text class="text-sm text-zinc-500">
            {{ __('Current photos. Hover to set cover or delete.') }}
        </flux:text>

        <div class="grid grid-cols-3 gap-4">
            @foreach ($existingPhotos as $photo)
                <div class="relative group">
                    <img
                        src="{{ $photo->public_url }}"
                        class="w-full h-32 object-cover rounded-lg border-2 transition
                            {{ $photo->is_primary
                                ? 'border-blue-500'
                                : 'border-zinc-200 dark:border-zinc-700' }}"
                        alt="{{ $photo->file_name }}"
                    />

                    @if ($photo->is_primary)
                        <span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                            {{ __('Cover') }}
                        </span>
                    @endif

                    <div class="absolute inset-0 bg-black/40 rounded-lg opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">

                        @if (!$photo->is_primary)
                            <button
                                wire:click="setExistingPrimary('{{ $photo->id }}')"
                                class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full hover:bg-blue-700 transition"
                                type="button"
                            >
                                {{ __('Set Cover') }}
                            </button>
                        @endif

                        <button
                            wire:click="deletePhoto('{{ $photo->id }}')"
                            wire:confirm="{{ __('Delete this photo?') }}"
                            class="bg-red-600 text-white text-xs px-2 py-1 rounded-full hover:bg-red-700 transition"
                            type="button"
                        >
                            {{ __('Delete') }}
                        </button>
                        {{--
                            wire:confirm → Livewire built-in confirmation dialog.
                            Shows a browser confirm() popup before running the method.
                            Prevents accidental deletions.
                            No JavaScript needed.
                        --}}

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 p-6 text-center">
            <flux:text class="text-zinc-500">{{ __('No photos yet.') }}</flux:text>
        </div>
    @endif

    {{-- Upload new photos --}}
    <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-3">
        <flux:text class="text-sm font-medium dark:text-white">
            {{ __('Add More Photos') }}
        </flux:text>

        <input
            type="file"
            wire:model="newPhotos"
            multiple
            accept="image/jpeg,image/jpg,image/png,image/webp"
            class="w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
        />
        @error('newPhotos')
            <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
        @enderror
        @error('newPhotos.*')
            <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
        @enderror

        {{-- New photo previews --}}
        @if (count($newPhotos) > 0)
            <div class="grid grid-cols-3 gap-4">
                @foreach ($newPhotos as $photo)
                    <img
                        src="{{ $photo->temporaryUrl() }}"
                        class="w-full h-32 object-cover rounded-lg border border-zinc-200 dark:border-zinc-700"
                        alt="New photo"
                    />
                @endforeach
            </div>

            <flux:button
                wire:click="uploadNewPhotos"
                variant="ghost">
                {{ __('Upload') }} {{ count($newPhotos) }} {{ __('photo(s)') }}
            </flux:button>
        @endif
    </div>

</div>
{{-- ===================== END EXISTING PHOTOS ===================== --}}

        {{-- Submit --}}
        <div class="flex justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button href="{{ route('assets.index') }}" wire:navigate variant="ghost">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button wire:click="save" variant="primary">
                {{ __('Save Changes') }}
            </flux:button>
        </div>

    </div>
</div>