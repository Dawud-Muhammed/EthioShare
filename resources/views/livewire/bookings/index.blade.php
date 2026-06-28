{{-- resources/views/livewire/bookings/index.blade.php --}}

<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- ===================== PAGE HEADER ===================== --}}
    <div class="flex items-center justify-between mb-6">
        <flux:heading size="xl">{{ __('My Bookings') }}</flux:heading>
    </div>

    {{-- ===================== TABS ===================== --}}
    <div class="flex border-b border-zinc-200 dark:border-zinc-700 mb-6">
        <button
            wire:click="$set('activeTab', 'renter')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                {{ $activeTab === 'renter'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('My Rentals') }}
        </button>

        <button
            wire:click="$set('activeTab', 'owner')"
            class="px-4 py-2 text-sm font-medium border-b-2 transition-colors
                {{ $activeTab === 'owner'
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}"
        >
            {{ __('My Asset Bookings') }}

            {{-- PENDING BADGE — shows count of bookings needing action --}}
            {{-- Why here and not on the sidebar?
                 This badge is scoped to this tab specifically.
                 It tells the owner exactly how many bookings
                 in THIS tab need their attention. --}}
            @if ($activeTab !== 'owner' && $this->pendingOwnerCount > 0)
                <span class="ml-2 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                    {{ $this->pendingOwnerCount }}
                </span>
            @endif
        </button>
    </div>
    {{-- ===================== END TABS ===================== --}}


    {{-- ===================== STATUS FILTER ===================== --}}
    <div class="mb-6">
        <flux:select
            wire:model="statusFilter"
            class="w-56"
        >
            <option value="">{{ __('All statuses') }}</option>
            @foreach ($this->statusOptions as $status)
                <option value="{{ $status->value }}">
                    {{ ucfirst(strtolower(str_replace('_', ' ', $status->value))) }}
                </option>
            @endforeach
        </flux:select>
    </div>
    {{-- ===================== END STATUS FILTER ===================== --}}


    {{-- ===================== BOOKING LIST ===================== --}}
    <div
        wire:loading.class="opacity-50"
        class="space-y-4 transition-opacity duration-150"
    >
        @if ($this->bookings->isEmpty())
            <div class="text-center py-16">
                <flux:heading>{{ __('No bookings found') }}</flux:heading>
                <flux:text class="mt-2 text-zinc-500">
                    @if ($activeTab === 'renter')
                        {{ __("You haven't made any bookings yet.") }}
                        <a href="{{ route('browse.assets') }}"
                           wire:navigate
                           class="text-blue-600 hover:underline ml-1">
                            {{ __('Browse assets') }}
                        </a>
                    @else
                        {{ __('No one has booked your assets yet.') }}
                    @endif
                </flux:text>
            </div>

        @else
            @foreach ($this->bookings as $booking)

                {{-- Why <a> wrapping the whole card?
                     Entire card is one clickable unit.
                     The owner or renter clicks anywhere on the
                     card and lands on the booking detail page.
                     Note: <a> must have the full tag — href on
                     its own without <a> is broken HTML. --}}
                <a href="{{ route('bookings.show', $booking) }}" wire:navigate class="block bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-5 hover:border-blue-300 dark:hover:border-blue-600 hover:shadow-sm transition-all">
                    <div class="flex items-start justify-between gap-4">

                        {{-- LEFT: ASSET + PEOPLE + DATES --}}
                        <div class="flex-1 min-w-0 space-y-1">

                            <p class="font-semibold text-zinc-900 dark:text-white truncate">
                                {{ $booking->asset->title }}
                            </p>

                            {{-- Why conditional on activeTab?
                                 Renter already knows they're the renter —
                                 showing the owner name is useful context.
                                 Owner already knows they're the owner —
                                 showing the renter name is useful context. --}}
                            <p class="text-sm text-zinc-500">
                                @if ($activeTab === 'renter')
                                    {{ __('Owner') }}: {{ $booking->owner->first_name }} {{ $booking->owner->last_name }}
                                @else
                                    {{ __('Renter') }}: {{ $booking->renter->first_name }} {{ $booking->renter->last_name }}
                                @endif
                            </p>

                            <p class="text-sm text-zinc-500">
                                {{ $booking->start_datetime->format('M j, Y · g:i A') }}
                                <span class="mx-1">→</span>
                                {{ $booking->end_datetime->format('M j, Y · g:i A') }}
                            </p>

                            {{-- QUICK ACTION HINT
                                 Why show this on the card?
                                 The owner sees "Action required" and knows
                                 clicking this card needs their attention.
                                 Saves them from clicking every PENDING card
                                 to find the ones that need action. --}}
                            @if ($activeTab === 'owner' && $booking->booking_status->value === 'PENDING')
                                <span class="inline-block mt-1 text-xs font-medium text-amber-600 dark:text-amber-400">
                                    ⚡ {{ __('Action required — confirm or cancel') }}
                                </span>
                            @endif

                            @if ($activeTab === 'owner' && $booking->booking_status->value === 'RENTER_ARRIVED')
                                <span class="inline-block mt-1 text-xs font-medium text-purple-600 dark:text-purple-400">
                                    ⚡ {{ __('Renter has arrived — complete handoff') }}
                                </span>
                            @endif

                            @if ($activeTab === 'owner' && $booking->booking_status->value === 'IN_PROGRESS')
                                <span class="inline-block mt-1 text-xs font-medium text-green-600 dark:text-green-400">
                                    🔄 {{ __('Rental in progress') }}
                                </span>
                            @endif

                        </div>

                        {{-- RIGHT: STATUS BADGE + AMOUNT --}}
                        <div class="flex flex-col items-end gap-2 flex-shrink-0">

                            @php
                                $statusColors = [
                                    'PENDING'        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                    'CONFIRMED'      => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                    'RENTER_ARRIVED' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                                    'IN_PROGRESS'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                    'COMPLETED'      => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300',
                                    'CANCELLED'      => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                ];
                                $color = $statusColors[$booking->booking_status->value] ?? 'bg-zinc-100 text-zinc-800';
                            @endphp

                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $color }}">
                                {{ ucfirst(strtolower(str_replace('_', ' ', $booking->booking_status->value))) }}
                            </span>

                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">
                                ETB {{ number_format($booking->total_charged, 2) }}
                            </p>

                        </div>

                    </div>
                </a>

            @endforeach
        @endif
    </div>
    {{-- ===================== END BOOKING LIST ===================== --}}


    {{-- ===================== PAGINATION ===================== --}}
    {{-- Why outside the wire:loading div?
         Pagination must always be visible and clickable,
         even while a new page is loading. --}}
    <div class="mt-6">
        {{ $this->bookings->links() }}
    </div>
    {{-- ===================== END PAGINATION ===================== --}}

</div>