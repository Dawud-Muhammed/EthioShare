<div class="max-w-6xl mx-auto py-8 px-4">

    {{-- ===================== HEADER ===================== --}}
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('My Assets') }}</flux:heading>
        <flux:button href="{{ route('assets.create') }}" wire:navigate variant="primary">
            + {{ __('List New Asset') }}
        </flux:button>
    </div>

    {{-- ===================== FLASH MESSAGE ===================== --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4">
            <flux:text class="text-green-700 dark:text-green-400">
                {{ session('success') }}
            </flux:text>
        </div>
    @endif
    {{--
        session('success') reads the flash message from CreateAssetForm.
        Flash messages survive exactly ONE redirect then disappear.
        After this render, it is gone automatically.
    --}}

    {{-- ===================== FILTER BAR ===================== --}}
    <div class="mb-6">
        <flux:select wire:model.live="statusFilter" class="w-48">
        {{--
            wire:model.live → updates $statusFilter on EVERY change,
            not just on form submit. As soon as owner picks a status,
            the list re-filters instantly. No button click needed.
            This is the difference between wire:model and wire:model.live.
        --}}
            <option value="">{{ __('All Statuses') }}</option>
            @foreach ($statuses as $status)
                <option value="{{ $status }}">{{ $status }}</option>
            @endforeach
        </flux:select>
    </div>
    {{-- ===================== END FILTER BAR ===================== --}}


    {{-- ===================== ASSETS TABLE ===================== --}}
    @if ($assets->isEmpty())
        <div class="text-center py-16">
            <flux:heading>{{ __('No assets yet') }}</flux:heading>
            <flux:text class="mt-2 text-zinc-500">
                {{ __('Start by listing your first asset.') }}
            </flux:text>
            <div class="mt-6">
                <flux:button href="{{ route('assets.create') }}" wire:navigate variant="primary">
                    {{ __('List Your First Asset') }}
                </flux:button>
            </div>
        </div>
    @else
        <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500">{{ __('Asset') }}</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500">{{ __('Type') }}</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500">{{ __('Daily Rate') }}</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500">{{ __('Status') }}</th>
                        <th class="text-left px-4 py-3 font-medium text-zinc-500">{{ __('Photos') }}</th>
                        <th class="text-right px-4 py-3 font-medium text-zinc-500">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($assets as $asset)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            {{-- Asset name + thumbnail --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($asset->media->isNotEmpty())
                                        <img
                                            src="{{ $asset->media->first()->public_url }}"
                                            class="w-10 h-10 rounded object-cover"
                                            alt="{{ $asset->title }}"
                                        />
                                    @else
                                        <div class="w-10 h-10 rounded bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center">
                                            <span class="text-xs text-zinc-400">?</span>
                                        </div>
                                    @endif
                                    <span class="font-medium dark:text-white">{{ $asset->title }}</span>
                                </div>
                            </td>

                            {{-- Type --}}
                            <td class="px-4 py-3 text-zinc-500">
                                {{ $asset->asset_type->value ?? $asset->asset_type }}
                            </td>

                            {{-- Daily rate --}}
                            <td class="px-4 py-3 text-zinc-500">
                                {{ number_format($asset->daily_rate, 2) }} ETB
                            </td>

                            {{-- Status badge --}}
                            <td class="px-4 py-3">
                                @php
                                    $statusColor = match($asset->status->value ?? $asset->status) {
                                        'ACTIVE'   => 'green',
                                        'DRAFT'    => 'yellow',
                                        'PAUSED'   => 'orange',
                                        'RENTED'   => 'blue',
                                        'DELISTED' => 'red',
                                        'ARCHIVED' => 'zinc',
                                        default    => 'zinc',
                                    };
                                @endphp
                                <flux:badge :color="$statusColor">
                                    {{ $asset->status->value ?? $asset->status }}
                                </flux:badge>
                                {{--
                                    match() expression maps each status to a Flux badge color.
                                    ACTIVE = green (good), DRAFT = yellow (pending),
                                    PAUSED = orange (warning), DELISTED = red (stopped).
                                    Visual language that owners understand instantly.
                                --}}
                            </td>

                            {{-- Photo count --}}
                            <td class="px-4 py-3 text-zinc-500">
                                {{ $asset->media_count }}
                                {{-- media_count comes from withCount('media') in the query --}}
                            </td>

                            {{-- Action buttons --}}
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    @if (($asset->status->value ?? $asset->status) !== 'DELISTED')
                                    <flux:button
                                        href="{{ route('assets.edit', $asset->id) }}"
                                        wire:navigate
                                        variant="ghost">
                                        {{ __('Edit') }}
                                    </flux:button>
                                    @else
                                    <flux:button>
                                        {{ __('No More Action') }}
                                    </flux:button>
                                    @endif
                                    @if (($asset->status->value ?? $asset->status) === 'DRAFT')
                                        <flux:button
                                            href="{{ route('assets.show', $asset->id) }}"
                                            wire:navigate
                                            variant="primary">
                                            {{ __('Publish') }}
                                        </flux:button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $assets->links() }}
            {{--
                ->links() renders Laravel's pagination UI automatically.
                WithPagination trait makes this work with Livewire.
                Clicking page numbers updates the list without page reload.
            --}}
        </div>
    @endif
    {{-- ===================== END ASSETS TABLE ===================== --}}

</div>