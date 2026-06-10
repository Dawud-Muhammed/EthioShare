<div class="max-w-3xl mx-auto py-8 px-4">

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="mb-8">
        <flux:heading size="xl">{{ __('List Your Asset') }}</flux:heading>
        <flux:text class="mt-1 text-zinc-500">
            {{ __('Step') }} {{ $currentStep }} {{ __('of') }} {{ $totalSteps }}
        </flux:text>
    </div>

    {{-- ===================== PROGRESS BAR ===================== --}}
    <div class="mb-8">
        <div class="flex items-center gap-2">
            @for ($i = 1; $i <= $totalSteps; $i++)
                <div class="flex-1 h-2 rounded-full {{ $i <= $currentStep ? 'bg-green-200 dark:bg-green-1700' : 'bg-red-200 dark:bg-red-1700' }}">
                </div>
            @endfor
        </div>
        <div class="flex justify-between mt-2 text-xs text-zinc-500">
            <span>{{ __('Details') }}</span>
            <span>{{ __('Photos') }}</span>
            <span>{{ __('Review') }}</span>
        </div>
    </div>
    {{-- ===================== END PROGRESS BAR ===================== --}}


    {{-- ===================== STEP 1: ASSET DETAILS ===================== --}}
    @if ($currentStep === 1)
    <div class="space-y-6">

        {{-- Basic Info --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Basic Information') }}</flux:heading>

            <flux:input
                wire:model="title"
                :label="__('Asset Title')"
                :placeholder="__('e.g. CAT 320D Excavator')"
                :description="__('Give your asset a clear, descriptive name.')"
            />

            <flux:textarea
                wire:model="description"
                :label="__('Description')"
                :placeholder="__('Describe your asset, its features, and any important details...')"
                rows="3"
            />
            @error('description')
                <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
            @enderror

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:select
                        wire:model="asset_type"
                        :label="__('Asset Type')"
                    >
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
                    <flux:select
                        wire:model="condition"
                        :label="__('Condition')"
                    >
                        <option value="">{{ __('Select condition...') }}</option>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition }}">{{ $condition }}</option>
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
                    <flux:input
                        wire:model="hourly_rate"
                        :label="__('Hourly Rate (ETB)')"
                        type="number"
                        min="0"
                        placeholder="0.00"
                    />
                    @error('hourly_rate')
                        <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div>
                    <flux:input
                        wire:model="daily_rate"
                        :label="__('Daily Rate (ETB)')"
                        type="number"
                        min="0"
                        placeholder="0.00"
                    />
                    @error('daily_rate')
                        <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div>
                    <flux:input
                        wire:model="security_deposit"
                        :label="__('Security Deposit (ETB)')"
                        type="number"
                        min="0"
                        placeholder="0.00"
                    />
                    @error('security_deposit')
                        <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>

                <div>
                    <flux:input
                        wire:model="estimated_value"
                        :label="__('Estimated Asset Value (ETB)')"
                        type="number"
                        min="0"
                        placeholder="0.00"
                    />
                    @error('estimated_value')
                        <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Location') }}</flux:heading>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:select
                        wire:model="region"
                        :label="__('Region')"
                    >
                        <option value="">{{ __('Select region...') }}</option>
                        @foreach ($ethiopianRegions as $region)
                            <option value="{{ $region }}">{{ $region }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div>
                    <flux:select
                        wire:model="delivery_method"
                        :label="__('Delivery Method')"
                    >
                        <option value="">{{ __('Select method...') }}</option>
                        @foreach ($deliveryMethods as $method)
                            <option value="{{ $method }}">{{ $method }}</option>
                        @endforeach
                    </flux:select>
                </div>
            </div>

            <flux:input
                wire:model="address_line"
                :label="__('Pickup Address')"
                :placeholder="__('e.g. Bole Sub-City, Woreda 03, near Edna Mall')"
            />
        </div>

        {{-- Availability --}}
        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Availability') }}</flux:heading>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <flux:input
                        wire:model="available_from"
                        :label="__('Available From')"
                        type="date"
                    />
                </div>

                <div>
                    <flux:input
                        wire:model="available_until"
                        :label="__('Available Until')"
                        type="date"
                    />
                </div>
            </div>
        </div>

        {{-- Step 1 Navigation --}}
        <div class="flex justify-end pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button wire:click="nextStep" variant="primary">
                {{ __('Next: Upload Photos') }} →
            </flux:button>
        </div>
    </div>
    {{-- ===================== END STEP 1 ===================== --}}


    {{-- ===================== STEP 2: PHOTOS ===================== --}}
    @elseif ($currentStep === 2)
    <div class="space-y-6">

        <div class="space-y-4">
            <flux:heading size="lg">{{ __('Upload Photos') }}</flux:heading>
            <flux:text class="text-zinc-500">
                {{ __('Upload at least 1 photo. First photo will be the cover image.') }}
            </flux:text>

            <div class="border-2 border-dashed border-zinc-300 dark:border-zinc-600 rounded-lg p-8 text-center">
                <input
                    type="file"
                    wire:model="photos"
                    multiple
                    accept="image/jpeg,image/jpg,image/png,image/webp"
                    class="w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
                <p class="mt-2 text-xs text-zinc-400">
                    {{ __('JPEG, PNG, WebP up to 10MB each. Maximum 10 photos.') }}
                </p>
            </div>

            @error('photos')
                <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
            @enderror
            @error('photos.*')
                <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
            @enderror

            {{-- Photo previews --}}
            @if (count($photos) > 0)
                <div class="grid grid-cols-3 gap-4 mt-4">
                    @foreach ($photos as $index => $photo)
                        <div class="relative group">
                            <img
                                src="{{ $photo->temporaryUrl() }}"
                                class="w-full h-32 object-cover rounded-lg border-2 transition
                                    {{ $index === $primaryIndex
                                        ? 'border-blue-500'
                                        : 'border-zinc-200 dark:border-zinc-700' }}"
                                alt="Photo {{ $index + 1 }}"
                            />

                            {{-- Primary badge --}}
                            @if ($index === $primaryIndex)
                                <span class="absolute top-1 left-1 bg-blue-600 text-white text-xs px-2 py-0.5 rounded-full">
                                    {{ __('Cover') }}
                                </span>
                            @endif

                            {{-- Action buttons — visible on hover --}}
                            <div class="absolute inset-0 bg-black/40 rounded-lg opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">

                                {{-- Set as cover button --}}
                                @if ($index !== $primaryIndex)
                                    <button
                                        wire:click="setPrimary({{ $index }})"
                                        class="bg-blue-600 text-white text-xs px-2 py-1 rounded-full hover:bg-blue-700 transition"
                                        type="button"
                                    >
                                        {{ __('Set Cover') }}
                                    </button>
                                @endif

                                {{-- Remove button --}}
                                <button
                                    wire:click="removePhoto({{ $index }})"
                                    class="bg-red-600 text-white text-xs px-2 py-1 rounded-full hover:bg-red-700 transition"
                                    type="button"
                                >
                                    {{ __('Remove') }}
                                </button>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Photo count indicator --}}
                <flux:text class="text-sm text-zinc-500 mt-2">
                    {{ count($photos) }} {{ __('photo(s) selected') }} · {{ __('Hover to set cover or remove') }}
                </flux:text>
            @endif
        
        </div>

        {{-- Step 2 Navigation --}}
        <div class="flex justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button wire:click="previousStep" variant="ghost">
                ← {{ __('Back') }}
            </flux:button>
            <flux:button wire:click="nextStep" variant="primary">
                {{ __('Next: Review') }} →
            </flux:button>
        </div>
    </div>
    {{-- ===================== END STEP 2 ===================== --}}


    {{-- ===================== STEP 3: REVIEW & PUBLISH ===================== --}}
    @elseif ($currentStep === 3)
    <div class="space-y-6">

        <flux:heading size="lg">{{ __('Review Your Listing') }}</flux:heading>

        {{-- Summary card --}}
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-zinc-500">{{ __('Title') }}</span>
                    <p class="font-medium dark:text-white">{{ $title }}</p>
                </div>
                <div>
                    <span class="text-zinc-500">{{ __('Type') }}</span>
                    <p class="font-medium dark:text-white">{{ $asset_type }}</p>
                </div>
                <div>
                    <span class="text-zinc-500">{{ __('Condition') }}</span>
                    <p class="font-medium dark:text-white">{{ $condition }}</p>
                </div>
                <div>
                    <span class="text-zinc-500">{{ __('Region') }}</span>
                    <p class="font-medium dark:text-white">{{ $region }}</p>
                </div>
                <div>
                    <span class="text-zinc-500">{{ __('Daily Rate') }}</span>
                    <p class="font-medium dark:text-white">{{ number_format((float)$daily_rate, 2) }} ETB</p>
                    {{-- number_format formats 1500 as 1,500.00 --}}
                </div>
                <div>
                    <span class="text-zinc-500">{{ __('Security Deposit') }}</span>
                    <p class="font-medium dark:text-white">{{ number_format((float)$security_deposit, 2) }} ETB</p>
                </div>
            </div>

            {{-- Status badge --}}
            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-700">
                <flux:badge color="yellow">{{ __('DRAFT') }}</flux:badge>
                <flux:text class="mt-1 text-sm text-zinc-500">
                    {{ __('Your asset is saved. Publish it to make it visible to renters.') }}
                </flux:text>
            </div>
        </div>

        {{-- Step 3 Navigation --}}
        <div class="flex justify-between pt-4 border-t border-zinc-200 dark:border-zinc-700">
            <flux:button wire:click="previousStep" variant="ghost">
                ← {{ __('Back') }}
            </flux:button>

            <div class="flex gap-3">
                <flux:button wire:click="saveDraft" variant="ghost">
                    {{ __('Save as Draft') }}
                </flux:button>
                <flux:button wire:click="publish" variant="primary">
                    {{ __('Publish Now') }}
                </flux:button>
            </div>
        </div>
    </div>
    @endif
    {{-- ===================== END STEP 3 ===================== --}}

</div>