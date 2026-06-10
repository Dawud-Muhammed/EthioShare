<div>

    {{-- ===================== HERO ===================== --}}
    <section class="bg-gradient-to-br from-zinc-900 to-zinc-800 py-16 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl text-zinc-600 dark:text-zinc-400 ">
                    
                    {{ __('Rent Any Asset in Ethiopia') }}
                </h1>
                <p class="mt-4 text-lg text-zinc-500 dark:text-zinc-400">
                    {{ __('Find machinery, vehicles, equipment and more across Ethiopian regions.') }}
                </p>

                {{-- Search bar --}}
                <div class="mt-8 mx-auto max-w-xl">
                    <div class="flex gap-2">
                        <flux:input
                            wire:model.live.debounce.400ms="search"
                            :placeholder="__('Search assets...')"
                            class="flex-1"
                        />
                        {{--
                            wire:model.live.debounce.400ms
                            live        → updates on every keystroke
                            debounce    → but waits 400ms after last keystroke
                            Why debounce? Without it, every character typed
                            triggers a database query. With 400ms debounce,
                            the query only runs when owner stops typing.
                            Prevents 10 queries for typing "excavator".
                            One query after they finish. Much better.
                        --}}
                    </div>
                </div>
            </div>
        </div>
    </section>
    {{-- ===================== END HERO ===================== --}}


    {{-- ===================== MAIN CONTENT ===================== --}}
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex gap-8">

            {{-- ===================== FILTER SIDEBAR ===================== --}}
            <aside class="hidden lg:block w-64 flex-shrink-0">
            {{--
                hidden    → invisible on mobile and tablet
                lg:block  → visible on large screens (1024px+)
                w-64      → fixed 256px width
                flex-shrink-0 → never compress when content area grows
            --}}
                <div class="sticky top-24 space-y-6">
                {{--
                    sticky top-24 → sidebar stays visible when scrolling.
                    top-24 = 96px from top (below the 64px navbar + some gap).
                --}}

                    <div class="flex items-center justify-between">
                        <flux:heading>{{ __('Filters') }}</flux:heading>
                        @if ($region || $assetType || $condition || $deliveryMethod || $minPrice || $maxPrice)
                            <flux:button wire:click="clearFilters" variant="ghost">
                                {{ __('Clear all') }}
                            </flux:button>
                            {{--
                                Only shows when at least one filter is active.
                                @if checks all filter properties — if any is non-empty,
                                show the clear button. Clean UX.
                            --}}
                        @endif
                    </div>

                    {{-- Region filter --}}
                    <div>
                        <flux:select wire:model.live="region" :label="__('Region')">
                            <option value="">{{ __('All Regions') }}</option>
                            @foreach ($ethiopianRegions as $reg)
                                <option value="{{ $reg }}">{{ $reg }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Asset type filter --}}
                    <div>
                        <flux:select wire:model.live="assetType" :label="__('Asset Type')">
                            <option value="">{{ __('All Types') }}</option>
                            @foreach ($assetTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Condition filter --}}
                    <div>
                        <flux:select wire:model.live="condition" :label="__('Condition')">
                            <option value="">{{ __('Any Condition') }}</option>
                            @foreach ($conditions as $cond)
                                <option value="{{ $cond }}">{{ $cond }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Delivery method filter --}}
                    <div>
                        <flux:select wire:model.live="deliveryMethod" :label="__('Delivery')">
                            <option value="">{{ __('Any Delivery') }}</option>
                            @foreach ($deliveryMethods as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </flux:select>
                    </div>

                    {{-- Price range --}}
                    <div class="space-y-2">
                        <flux:text class="text-sm font-medium dark:text-white">
                            {{ __('Daily Rate (ETB)') }}
                        </flux:text>
                        <div class="grid grid-cols-2 gap-2">
                            <flux:input
                                wire:model.live.debounce.600ms="minPrice"
                                type="number"
                                min="0"
                                :placeholder="__('Min')"
                            />
                            <flux:input
                                wire:model.live.debounce.600ms="maxPrice"
                                type="number"
                                min="0"
                                :placeholder="__('Max')"
                            />
                        </div>
                        @if ($minPrice && $maxPrice && (float)$maxPrice <= (float)$minPrice)
                            <flux:text class="text-red-500 text-xs">
                                {{ __('Max must be greater than min.') }}
                            </flux:text>
                        @endif
                    </div>

                </div>
            </aside>
            {{-- ===================== END FILTER SIDEBAR ===================== --}}


            {{-- ===================== ASSET GRID ===================== --}}
            <main class="flex-1 min-w-0">
            {{-- flex-1 → takes all remaining space after sidebar --}}
            {{-- min-w-0 → prevents flex child from overflowing --}}

                {{-- Results count --}}
                <div class="mb-4 flex items-center justify-between">
                    <flux:text class="text-zinc-500">
                        {{ $assets->total() }} {{ __('assets found') }}
                    </flux:text>
                </div>

                @if ($assets->isEmpty())
                    {{-- Empty state --}}
                    <div class="text-center py-16">
                        <flux:heading>{{ __('No assets found') }}</flux:heading>
                        <flux:text class="mt-2 text-zinc-500">
                            {{ __('Try adjusting your filters.') }}
                        </flux:text>
                        <div class="mt-4">
                            <flux:button wire:click="clearFilters" variant="ghost">
                                {{ __('Clear Filters') }}
                            </flux:button>
                        </div>
                    </div>
                @else
                    {{-- Asset grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    {{--
                        grid-cols-1     → 1 column on mobile
                        sm:grid-cols-2  → 2 columns on small screens
                        xl:grid-cols-3  → 3 columns on large screens
                        Responsive grid that adapts to screen size automatically.
                    --}}
                        @foreach ($assets as $asset)
                            <a
                                href="{{ route('assets.show', $asset->id) }}"
                                wire:navigate
                                class="group block rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden hover:border-blue-500 dark:hover:border-blue-500 transition-all hover:shadow-lg"
                            >
                            {{--
                                group → Tailwind group modifier.
                                Lets child elements react to parent hover.
                                group-hover:scale-105 on the image below
                                reacts when the card (group) is hovered.
                            --}}

                                {{-- Asset photo --}}
                                <div class="aspect-video bg-zinc-100 dark:bg-zinc-800 overflow-hidden">
                                {{--
                                    aspect-video → maintains 16:9 ratio automatically.
                                    No fixed height needed. Adapts to container width.
                                    overflow-hidden → clips the scaled image cleanly.
                                --}}
                                    @if ($asset->media->isNotEmpty())
                                        <img
                                            src="{{ $asset->media->first()->public_url }}"
                                            alt="{{ $asset->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                        />
                                        {{--
                                            object-cover → fills container without distortion.
                                            group-hover:scale-105 → subtle zoom on card hover.
                                            duration-300 → 300ms smooth transition.
                                        --}}
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-zinc-400 text-sm">{{ __('No photo') }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Asset info --}}
                                <div class="p-4 space-y-2">

                                    {{-- Title and type --}}
                                    <div>
                                        <h3 class="font-semibold text-zinc-900 dark:text-white truncate">
                                            {{ $asset->title }}
                                        </h3>
                                        <flux:text class="text-sm text-zinc-500">
                                            {{ $asset->asset_type->value ?? $asset->asset_type }}
                                            ·
                                            {{ $asset->condition->value ?? $asset->condition }}
                                        </flux:text>
                                    </div>

                                    {{-- Region --}}
                                    <flux:text class="text-sm text-zinc-500">
                                        📍 {{ $asset->region }}
                                    </flux:text>

                                    {{-- Price --}}
                                    <div class="flex items-center justify-between pt-2 border-t border-zinc-100 dark:border-zinc-700">
                                        <div>
                                            <span class="text-lg font-bold text-zinc-900 dark:text-white">
                                                {{ number_format($asset->daily_rate, 0) }}
                                            </span>
                                            <span class="text-sm text-zinc-500"> ETB/day</span>
                                        </div>

                                        {{-- Owner info --}}
                                        @if ($asset->owner)
                                            <flux:text class="text-xs text-zinc-400">
                                                {{ $asset->owner->first_name }}
                                            </flux:text>
                                        @endif
                                    </div>

                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $assets->links() }}
                    </div>
                @endif

            </main>
            {{-- ===================== END ASSET GRID ===================== --}}

        </div>
    </div>
    {{-- ===================== END MAIN CONTENT ===================== --}}

</div>