<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- ===================== BREADCRUMB ===================== --}}
    <nav class="mb-6 text-sm text-zinc-500 flex items-center gap-2">
        <a href="{{ route('browse.assets') }}" wire:navigate
           class="hover:text-zinc-900 dark:hover:text-white transition">
            {{ __('Browse Assets') }}
        </a>
        <span>/</span>
        <span class="text-zinc-900 dark:text-white truncate">{{ $asset->title }}</span>
    </nav>
    {{-- ===================== END BREADCRUMB ===================== --}}


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{--
        1 column on mobile → stacks vertically
        3 columns on large → photos take 2 cols, booking panel takes 1
    --}}

        {{-- ===================== LEFT: PHOTOS + DETAILS ===================== --}}
        <div class="lg:col-span-2 space-y-6">
        {{-- lg:col-span-2 → takes 2 out of 3 columns on large screens --}}

            {{-- Main photo --}}
            <div class="aspect-video rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                @if ($asset->media->isNotEmpty())
                    <img
                        src="{{ $asset->media->get($activePhotoIndex)?->public_url ?? $asset->media->first()->public_url }}"
                        alt="{{ $asset->title }}"
                        class="w-full h-full object-cover"
                    />
                    {{--
                        ->get($activePhotoIndex) → gets photo at the active index.
                        ?? $asset->media->first() → fallback if index doesn't exist.
                        This prevents errors if owner has 3 photos and $activePhotoIndex is 5.
                    --}}
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-zinc-400">{{ __('No photos uploaded') }}</span>
                    </div>
                @endif
            </div>

            {{-- Photo thumbnails --}}
            @if ($asset->media->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-2">
                {{-- overflow-x-auto → horizontal scroll if many thumbnails --}}
                    @foreach ($asset->media as $index => $photo)
                        <button
                            wire:click="setActivePhoto({{ $index }})"
                            class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition
                                {{ $activePhotoIndex === $index
                                    ? 'border-blue-500'
                                    : 'border-transparent hover:border-zinc-300' }}"
                        >
                        {{--
                            flex-shrink-0 → thumbnails never compress
                            border-blue-500 → active thumbnail has blue border
                            border-transparent → inactive thumbnails have no visible border
                            Ternary inside class is Blade's way of conditional classes.
                        --}}
                            <img
                                src="{{ $photo->public_url }}"
                                alt="Photo {{ $index + 1 }}"
                                class="w-full h-full object-cover"
                            />
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Asset title and badges --}}
            <div>
                <div class="flex items-start justify-between gap-4">
                    <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                        {{ $asset->title }}
                    </h1>
                    @if ($isOwner)
                        <flux:button
                            href="{{ route('assets.edit', $asset->id) }}"
                            wire:navigate
                            variant="ghost">
                            {{ __('Edit') }}
                        </flux:button>
                    @endif
                </div>

                <div class="mt-2 flex items-center gap-3 flex-wrap">
                    <flux:badge color="zinc">
                        {{ $asset->asset_type->value ?? $asset->asset_type }}
                    </flux:badge>
                    <flux:badge color="zinc">
                        {{ $asset->condition->value ?? $asset->condition }}
                    </flux:badge>
                    @if ($isOwner)
                        @php
                            $statusColor = match($asset->status->value ?? $asset->status) {
                                'ACTIVE'   => 'green',
                                'DRAFT'    => 'yellow',
                                'PAUSED'   => 'orange',
                                'DELISTED' => 'red',
                                default    => 'zinc',
                            };
                        @endphp
                        <flux:badge :color="$statusColor">
                            {{ $asset->status->value ?? $asset->status }}
                        </flux:badge>
                        {{-- Status badge only visible to owner --}}
                    @endif
                </div>
            </div>

            {{-- Description --}}
            @if ($asset->description)
                <div class="prose dark:prose-invert max-w-none">
                    <flux:heading size="lg">{{ __('About this asset') }}</flux:heading>
                    <p class="mt-2 text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {{ $asset->description }}
                    </p>
                </div>
            @endif

            {{-- Details grid --}}
            <div>
                <flux:heading size="lg">{{ __('Details') }}</flux:heading>
                <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3">
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                        <flux:text class="text-xs text-zinc-500">{{ __('Region') }}</flux:text>
                        <p class="mt-1 font-medium dark:text-white">{{ $asset->region }}</p>
                    </div>
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                        <flux:text class="text-xs text-zinc-500">{{ __('Pickup Address') }}</flux:text>
                        <p class="mt-1 font-medium dark:text-white text-sm">{{ $asset->address_line }}</p>
                    </div>
                    @if ($asset->delivery_method)
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                            <flux:text class="text-xs text-zinc-500">{{ __('Delivery') }}</flux:text>
                            <p class="mt-1 font-medium dark:text-white text-sm">
                                {{ $asset->delivery_method->value ?? $asset->delivery_method }}
                            </p>
                        </div>
                    @endif
                    @if ($asset->available_from)
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                            <flux:text class="text-xs text-zinc-500">{{ __('Available From') }}</flux:text>
                            <p class="mt-1 font-medium dark:text-white">
                                {{ $asset->available_from->format('M d, Y') }}
                            </p>
                        </div>
                    @endif
                    @if ($asset->available_until)
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                            <flux:text class="text-xs text-zinc-500">{{ __('Available Until') }}</flux:text>
                            <p class="mt-1 font-medium dark:text-white">
                                {{ $asset->available_until->format('M d, Y') }}
                            </p>
                        </div>
                    @endif
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-4">
                        <flux:text class="text-xs text-zinc-500">{{ __('Service Radius') }}</flux:text>
                        <p class="mt-1 font-medium dark:text-white">
                            {{ $asset->service_radius_km }} km
                        </p>
                    </div>
                </div>
            </div>

        </div>
        {{-- ===================== END LEFT COLUMN ===================== --}}


        {{-- ===================== RIGHT: BOOKING PANEL ===================== --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-4">
            {{-- sticky top-24 → booking panel stays visible when scrolling --}}

                {{-- Pricing card --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">

                    <div>
                        <span class="text-3xl font-bold text-zinc-900 dark:text-white">
                            {{ number_format($asset->daily_rate, 0) }}
                        </span>
                        <span class="text-zinc-500"> ETB/day</span>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">{{ __('Hourly rate') }}</span>
                            <span class="dark:text-white">{{ number_format($asset->hourly_rate, 0) }} ETB</span>
                        </div>
                        @if ($asset->weekly_rate)
                            <div class="flex justify-between">
                                <span class="text-zinc-500">{{ __('Weekly rate') }}</span>
                                <span class="dark:text-white">{{ number_format($asset->weekly_rate, 0) }} ETB</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-500">{{ __('Security deposit') }}</span>
                            <span class="dark:text-white">{{ number_format($asset->security_deposit, 0) }} ETB</span>
                        </div>
                    </div>

                    {{-- Book button or owner message --}}
                        @if ($isOwner)
                            <div class="flex items-center gap-2 flex-wrap">

                                <flux:button
                                    href="{{ route('assets.edit', $asset->id) }}"
                                    wire:navigate
                                    variant="ghost">
                                    {{ __('Edit') }}
                                </flux:button>

                                {{-- Publish button — only for DRAFT assets --}}
                                @if (($asset->status->value ?? $asset->status) === 'DRAFT')
                                    <flux:button
                                        wire:click="publishAsset"
                                        variant="primary">
                                        {{ __('Publish') }}
                                    </flux:button>
                                @endif

                                {{-- Pause button — only for ACTIVE assets --}}
                                @if (($asset->status->value ?? $asset->status) === 'ACTIVE')
                                    <flux:button
                                        wire:click="updateStatus('PAUSED')"
                                        variant="ghost">
                                        {{ __('Pause Listing') }}
                                    </flux:button>
                                @endif

                                {{-- Resume button — only for PAUSED assets --}}
                                @if (($asset->status->value ?? $asset->status) === 'PAUSED')
                                    <flux:button
                                        wire:click="updateStatus('ACTIVE')"
                                        variant="primary">
                                        {{ __('Resume Listing') }}
                                    </flux:button>
                                @endif

                                {{-- Delist button — for ACTIVE or PAUSED assets --}}
                                @if (in_array(
                                    ($asset->status->value ?? $asset->status),
                                    ['ACTIVE', 'PAUSED']
                                ))
                                    <flux:button
                                        x-on:click="$flux.modal('delist-modal').show()"
                                        variant="danger"
                                        class="w-full">
                                        {{ __('Delist Asset') }}
                                    </flux:button>
                                @endif

                            </div>
                        @else
                        <flux:button variant="primary" class="w-full">
                            {{ __('Request to Book') }}
                        </flux:button>
                        {{--
                            TODO: Phase D — wire:click="requestBooking"
                            This button does nothing yet. It is a placeholder
                            that shows renters the UI they will use in Phase D.
                            Never show a broken button. A placeholder with
                            coming-soon behavior is always better than a 500 error.
                        --}}
                        <flux:text class="text-xs text-center text-zinc-400">
                            {{ __('Booking system coming soon.') }}
                        </flux:text>
                    @endif

                </div>

                {{-- Owner card --}}
                @if ($asset->owner)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-2">
                        <flux:heading>{{ __('Listed by') }}</flux:heading>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                <span class="text-blue-700 dark:text-blue-300 font-semibold text-sm">
                                    {{ strtoupper(substr($asset->owner->first_name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium dark:text-white">
                                    {{ $asset->owner->first_name }} {{ $asset->owner->last_name }}
                                </p>
                                @if ($asset->owner->total_trust_score)
                                    <flux:text class="text-xs text-zinc-500">
                                        {{ __('Trust Score') }}: {{ $asset->owner->total_trust_score }}
                                    </flux:text>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
        {{-- ===================== END RIGHT COLUMN ===================== --}}
    {{-- ===================== DELIST MODAL ===================== --}}
    @if ($isOwner)
    <flux:modal name="delist-modal" class="max-w-md">
        <div class="space-y-4 p-4">
            <flux:heading>{{ __('Delist This Asset?') }}</flux:heading>
            <flux:text class="text-zinc-500">
                {{ __('This will permanently remove your asset from the marketplace. This action cannot be undone.') }}
            </flux:text>

            <div>
                <flux:textarea
                    wire:model="delistReason"
                    :label="__('Reason (required)')"
                    :placeholder="__('Why are you delisting this asset?')"
                    rows="3"
                />
                @error('delistReason')
                    <flux:text class="text-red-500 text-sm">{{ $message }}</flux:text>
                @enderror
            </div>

            <div class="flex justify-end gap-3">
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button
                    wire:click="delistAsset"
                    variant="danger">
                    {{ __('Confirm Delist') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
    {{-- ===================== END DELIST MODAL ===================== --}}
    </div>
</div>