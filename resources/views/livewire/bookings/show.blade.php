{{-- resources/views/livewire/bookings/show.blade.php --}}

<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- ===================== BACK BUTTON + HEADER ===================== --}}
    <div class="mb-6">
        <a
            href="{{ route('bookings.index') }}"
            wire:navigate
            class="inline-flex items-center gap-1 text-sm text-zinc-500 hover:text-zinc-900 dark:hover:text-white transition mb-4"
        >
            ← {{ __('Back to bookings') }}
        </a>

        <div class="flex items-start justify-between gap-4">
            <div>
                <flux:heading size="xl">
                    {{ $this->booking->asset->title }}
                </flux:heading>
                <flux:text class="text-zinc-500 mt-1">
                    {{ __('Booking') }} #{{ $this->booking->id }}
                </flux:text>
            </div>

            {{-- STATUS BADGE --}}
            @php
                $statusColors = [
                    'PENDING'        => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'CONFIRMED'      => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                    'RENTER_ARRIVED' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
                    'IN_PROGRESS'    => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                    'COMPLETED'      => 'bg-zinc-100 text-zinc-800 dark:bg-zinc-700 dark:text-zinc-300',
                    'CANCELLED'      => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                ];
                $statusColor = $statusColors[$this->booking->booking_status->value] ?? 'bg-zinc-100 text-zinc-800';
            @endphp

            <span class="px-4 py-2 rounded-full text-sm font-semibold {{ $statusColor }} flex-shrink-0">
                {{ ucfirst(strtolower(str_replace('_', ' ', $this->booking->booking_status->value))) }}
            </span>
        </div>
    </div>
    {{-- ===================== END HEADER ===================== --}}


    {{-- ===================== FLASH MESSAGES ===================== --}}
    @if ($successMessage)
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <flux:text class="text-green-700 dark:text-green-400 text-sm font-medium">
                ✓ {{ $successMessage }}
            </flux:text>
        </div>
    @endif

    @if ($errorMessage)
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <flux:text class="text-red-700 dark:text-red-400 text-sm font-medium">
                {{ $errorMessage }}
            </flux:text>
        </div>
    @endif
    {{-- ===================== END FLASH MESSAGES ===================== --}}


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===================== LEFT COLUMN: CONTEXT (2 cols) ===================== --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- RENTAL PERIOD CARD --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Rental Period') }}</flux:heading>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">
                            {{ __('Start') }}
                        </flux:text>
                        <p class="mt-1 font-semibold text-zinc-900 dark:text-white">
                            {{ $this->booking->start_datetime->format('D, M j Y') }}
                        </p>
                        <p class="text-zinc-500">
                            {{ $this->booking->start_datetime->format('g:i A') }}
                        </p>
                    </div>
                    <div>
                        <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">
                            {{ __('End') }}
                        </flux:text>
                        <p class="mt-1 font-semibold text-zinc-900 dark:text-white">
                            {{ $this->booking->end_datetime->format('D, M j Y') }}
                        </p>
                        <p class="text-zinc-500">
                            {{ $this->booking->end_datetime->format('g:i A') }}
                        </p>
                    </div>
                </div>

                {{-- Actual times — only show if rental has started --}}
                @if ($this->booking->actual_start_datetime)
                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-700 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">
                                {{ __('Actual Start') }}
                            </flux:text>
                            <p class="mt-1 font-medium text-zinc-900 dark:text-white">
                                {{ $this->booking->actual_start_datetime->format('M j · g:i A') }}
                            </p>
                        </div>
                        @if ($this->booking->actual_end_datetime)
                            <div>
                                <flux:text class="text-xs text-zinc-500 uppercase tracking-wide">
                                    {{ __('Actual End') }}
                                </flux:text>
                                <p class="mt-1 font-medium text-zinc-900 dark:text-white">
                                    {{ $this->booking->actual_end_datetime->format('M j · g:i A') }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
            {{-- END RENTAL PERIOD CARD --}}


            {{-- PEOPLE CARD --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Parties Involved') }}</flux:heading>

                <div class="grid grid-cols-2 gap-6">

                    {{-- RENTER --}}
                    <div>
                        <flux:text class="text-xs text-zinc-500 uppercase tracking-wide mb-2">
                            {{ __('Renter') }}
                        </flux:text>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-700 dark:text-blue-300 font-semibold text-sm">
                                    {{ strtoupper(substr($this->booking->renter->first_name, 0, 1)) }}{{ strtoupper(substr($this->booking->renter->last_name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white text-sm">
                                    {{ $this->booking->renter->first_name }}
                                    {{ $this->booking->renter->last_name }}
                                </p>
                                @if ($this->booking->renter->total_trust_score)
                                    <flux:text class="text-xs text-zinc-500">
                                        ⭐ {{ number_format($this->booking->renter->total_trust_score, 1) }}
                                    </flux:text>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- OWNER --}}
                    <div>
                        <flux:text class="text-xs text-zinc-500 uppercase tracking-wide mb-2">
                            {{ __('Owner') }}
                        </flux:text>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center flex-shrink-0">
                                <span class="text-green-700 dark:text-green-300 font-semibold text-sm">
                                    {{ strtoupper(substr($this->booking->owner->first_name, 0, 1)) }}{{ strtoupper(substr($this->booking->owner->last_name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white text-sm">
                                    {{ $this->booking->owner->first_name }}
                                    {{ $this->booking->owner->last_name }}
                                </p>
                                @if ($this->booking->owner->total_trust_score)
                                    <flux:text class="text-xs text-zinc-500">
                                        ⭐ {{ number_format($this->booking->owner->total_trust_score, 1) }}
                                    </flux:text>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            {{-- END PEOPLE CARD --}}


            {{-- HANDOFF INFO CARD --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Handoff Details') }}</flux:heading>

                <div class="text-sm space-y-3">
                    <div class="flex justify-between">
                        <flux:text class="text-zinc-500">{{ __('Method') }}</flux:text>
                        <span class="font-medium text-zinc-900 dark:text-white">
                            @switch($this->booking->handoff_method->value)
                                @case('QR_CODE')       {{ __('QR Code Handoff') }}      @break
                                @case('MANUAL_KEY')    {{ __('Manual Key Handover') }}  @break
                                @case('DIGITAL_ACCESS'){{ __('Digital Access Code') }}  @break
                                @case('LOCATION_PICKUP'){{ __('Location Pickup') }}     @break
                                @default               {{ $this->booking->handoff_method->value }}
                            @endswitch
                        </span>
                    </div>

                    @if ($this->booking->handoffLocation)
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500">{{ __('Location') }}</flux:text>
                            <span class="font-medium text-zinc-900 dark:text-white">
                                {{ $this->booking->handoffLocation->name }}
                            </span>
                        </div>
                    @endif

                    @if ($this->booking->handoff_completed_at)
                        <div class="flex justify-between">
                            <flux:text class="text-zinc-500">{{ __('Handoff completed') }}</flux:text>
                            <span class="font-medium text-zinc-900 dark:text-white">
                                {{ $this->booking->handoff_completed_at->format('M j · g:i A') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            {{-- END HANDOFF INFO CARD --}}


            {{-- PRICING CARD --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">
                <flux:heading size="lg" class="mb-4">{{ __('Pricing Breakdown') }}</flux:heading>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>{{ __('Rate applied') }}</span>
                        <span>ETB {{ number_format($this->booking->rate_applied, 2) }}/hr</span>
                    </div>
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>{{ __('Rental amount') }}</span>
                        <span>ETB {{ number_format($this->booking->total_rental_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                        <span>{{ __('Platform fee') }}</span>
                        <span>ETB {{ number_format($this->booking->platform_fee, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-zinc-600 dark:text-zinc-400 pb-3 border-b border-zinc-100 dark:border-zinc-700">
                        <span>{{ __('Security deposit') }}</span>
                        <span>ETB {{ number_format($this->booking->security_deposit_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-zinc-900 dark:text-white text-base">
                        <span>{{ __('Total charged') }}</span>
                        <span>ETB {{ number_format($this->booking->total_charged, 2) }}</span>
                    </div>
                </div>
            </div>
            {{-- END PRICING CARD --}}

        </div>
        {{-- ===================== END LEFT COLUMN ===================== --}}


        {{-- ===================== RIGHT COLUMN: ACTIONS (1 col) ===================== --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-4">

                {{-- ===================== ACTION PANEL ===================== --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6 space-y-4">

                    <flux:heading size="lg">{{ __('Actions') }}</flux:heading>

                    {{-- WHAT HAPPENS NEXT — context for current viewer --}}
                    <div class="rounded-lg bg-zinc-50 dark:bg-zinc-700 p-3">
                        <flux:text class="text-xs text-zinc-600 dark:text-zinc-300">
                            @switch($this->booking->booking_status->value)
                                @case('PENDING')
                                    @if ($this->isOwner)
                                        ⏳ {{ __('A renter has requested this asset. Review and confirm or cancel.') }}
                                    @else
                                        ⏳ {{ __('Waiting for the owner to confirm your request.') }}
                                    @endif
                                    @break

                                @case('CONFIRMED')
                                    @if ($this->isRenter)
                                        ✅ {{ __('Booking confirmed! Head to the handoff location and mark your arrival.') }}
                                    @else
                                        ✅ {{ __('Confirmed. Waiting for the renter to arrive.') }}
                                    @endif
                                    @break

                                @case('RENTER_ARRIVED')
                                    @if ($this->isOwner)
                                        👋 {{ __('The renter has arrived. Verify their identity and complete the handoff.') }}
                                    @else
                                        👋 {{ __('You have checked in. Waiting for the owner to complete the handoff.') }}
                                    @endif
                                    @break

                                @case('IN_PROGRESS')
                                    @if ($this->isOwner)
                                        🔄 {{ __('Rental in progress. Mark as returned when the asset is back.') }}
                                    @else
                                        🔄 {{ __('Rental in progress. Return the asset by the end time.') }}
                                    @endif
                                    @break

                                @case('COMPLETED')
                                    🎉 {{ __('This rental is complete.') }}
                                    @break

                                @case('CANCELLED')
                                    ❌ {{ __('This booking was cancelled.') }}
                                    @break
                            @endswitch
                        </flux:text>
                    </div>

                    {{-- ============================================
                         ACTION BUTTONS
                         Each button only renders when its computed
                         property returns true. All logic is in
                         BookingShow.php — not here.
                         ============================================ --}}

                    {{-- CONFIRM BOOKING — owner only, PENDING --}}
                    @if ($this->canConfirmBooking)
                        <flux:button
                            wire:click="confirmBooking"
                            wire:loading.attr="disabled"
                            variant="primary"
                            class="w-full">
                            <span wire:loading.remove wire:target="confirmBooking">
                                ✓ {{ __('Confirm Booking') }}
                            </span>
                            <span wire:loading wire:target="confirmBooking">
                                {{ __('Confirming...') }}
                            </span>
                        </flux:button>
                    @endif

                    {{-- CONFIRM ARRIVAL — renter only, CONFIRMED --}}
                    @if ($this->canArrive)
                        <flux:button
                            wire:click="confirmArrival"
                            wire:loading.attr="disabled"
                            variant="primary"
                            class="w-full">
                            <span wire:loading.remove wire:target="confirmArrival">
                                📍 {{ __("I've Arrived") }}
                            </span>
                            <span wire:loading wire:target="confirmArrival">
                                {{ __('Confirming...') }}
                            </span>
                        </flux:button>
                    @endif

                    {{-- COMPLETE HANDOFF — owner only, RENTER_ARRIVED --}}
                    @if ($this->canCompleteHandoff)
                        <flux:button
                            wire:click="completeHandoff"
                            wire:loading.attr="disabled"
                            variant="primary"
                            class="w-full">
                            <span wire:loading.remove wire:target="completeHandoff">
                                🤝 {{ __('Complete Handoff') }}
                            </span>
                            <span wire:loading wire:target="completeHandoff">
                                {{ __('Processing...') }}
                            </span>
                        </flux:button>
                    @endif

                    {{-- COMPLETE BOOKING — owner only, IN_PROGRESS --}}
                    @if ($this->canComplete)
                        <flux:button
                            wire:click="completeBooking"
                            wire:loading.attr="disabled"
                            variant="primary"
                            class="w-full">
                            <span wire:loading.remove wire:target="completeBooking">
                                ✅ {{ __('Mark as Returned') }}
                            </span>
                            <span wire:loading wire:target="completeBooking">
                                {{ __('Processing...') }}
                            </span>
                        </flux:button>
                    @endif

                    {{-- CANCEL — either party, PENDING or CONFIRMED --}}
                    @if ($this->canCancel)

                        @if (!$showCancelConfirm)
                            {{-- First click — show confirmation --}}
                            <flux:button
                                wire:click="$set('showCancelConfirm', true)"
                                variant="ghost"
                                class="w-full text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20">
                                {{ __('Cancel Booking') }}
                            </flux:button>

                        @else
                            {{-- CANCEL CONFIRMATION --}}
                            <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20 p-4 space-y-3">

                                <flux:text class="text-sm font-medium text-red-800 dark:text-red-400">
                                    {{ __('Are you sure you want to cancel?') }}
                                </flux:text>

                                <flux:textarea
                                    wire:model="cancelReason"
                                    :placeholder="__('Give a reason (minimum 10 characters)')"
                                    rows="3"
                                />

                                @error('cancelReason')
                                    <flux:text class="text-xs text-red-600 dark:text-red-400">
                                        {{ $message }}
                                    </flux:text>
                                @enderror

                                <div class="flex gap-2">
                                    <flux:button
                                        wire:click="cancelBooking"
                                        wire:loading.attr="disabled"
                                        variant="danger"
                                        class="flex-1">
                                        <span wire:loading.remove wire:target="cancelBooking">
                                            {{ __('Yes, Cancel') }}
                                        </span>
                                        <span wire:loading wire:target="cancelBooking">
                                            {{ __('Cancelling...') }}
                                        </span>
                                    </flux:button>

                                    <flux:button
                                        wire:click="$set('showCancelConfirm', false)"
                                        variant="ghost">
                                        {{ __('Go Back') }}
                                    </flux:button>
                                </div>
                            </div>
                        @endif

                    @endif

                    {{-- COMPLETED STATE — no actions, show summary --}}
                    @if ($this->booking->booking_status->value === 'COMPLETED')
                        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 p-4 text-center">
                            <flux:text class="text-sm text-green-700 dark:text-green-400 font-medium">
                                🎉 {{ __('Rental completed successfully') }}
                            </flux:text>
                            <flux:text class="text-xs text-green-600 dark:text-green-500 mt-1">
                                {{-- TODO: Phase 2 — show payout status for owner,
                                     show deposit refund status for renter --}}
                                {{ __('Payout is being processed.') }}
                            </flux:text>
                        </div>
                    @endif

                    {{-- CANCELLED STATE — no actions, show reason --}}
                    @if ($this->booking->booking_status->value === 'CANCELLED')
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 text-center">
                            <flux:text class="text-sm text-red-700 dark:text-red-400 font-medium">
                                {{ __('This booking was cancelled.') }}
                            </flux:text>
                        </div>
                    @endif

                </div>
                {{-- ===================== END ACTION PANEL ===================== --}}


                {{-- ===================== BOOKING STATUS TIMELINE ===================== --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">

                    <flux:heading size="sm" class="mb-4">{{ __('Booking Journey') }}</flux:heading>

                    @php
                        $allStatuses = ['PENDING', 'CONFIRMED', 'RENTER_ARRIVED', 'IN_PROGRESS', 'COMPLETED'];
                        $currentStatus = $this->booking->booking_status->value;
                        $isCancelled = $currentStatus === 'CANCELLED';

                        // Find the index of current status in the normal flow
                        $currentIndex = array_search($currentStatus, $allStatuses);
                    @endphp

                    @if ($isCancelled)
                        <div class="flex items-center gap-2 text-sm text-red-600 dark:text-red-400">
                            <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                            {{ __('Cancelled') }}
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach ($allStatuses as $index => $status)
                                @php
                                    $isPast    = $currentIndex !== false && $index < $currentIndex;
                                    $isCurrent = $status === $currentStatus;
                                    $isFuture  = $currentIndex !== false && $index > $currentIndex;

                                    $dotColor = $isPast
                                        ? 'bg-green-500'
                                        : ($isCurrent ? 'bg-blue-500 ring-4 ring-blue-100 dark:ring-blue-900' : 'bg-zinc-200 dark:bg-zinc-600');

                                    $labelColor = $isCurrent
                                        ? 'text-zinc-900 dark:text-white font-semibold'
                                        : ($isPast ? 'text-green-600 dark:text-green-400' : 'text-zinc-400 dark:text-zinc-500');

                                    $statusLabels = [
                                        'PENDING'        => __('Request sent'),
                                        'CONFIRMED'      => __('Owner confirmed'),
                                        'RENTER_ARRIVED' => __('Renter arrived'),
                                        'IN_PROGRESS'    => __('Rental started'),
                                        'COMPLETED'      => __('Rental completed'),
                                    ];
                                @endphp

                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 rounded-full flex-shrink-0 {{ $dotColor }}"></div>
                                    <span class="text-sm {{ $labelColor }}">
                                        {{ $statusLabels[$status] }}
                                        @if ($isPast) ✓ @endif
                                    </span>
                                </div>

                                {{-- Connecting line between dots --}}
                                @if (!$loop->last)
                                    <div class="ml-1.5 w-px h-4 {{ $isPast ? 'bg-green-300 dark:bg-green-700' : 'bg-zinc-200 dark:bg-zinc-600' }}"></div>
                                @endif

                            @endforeach
                        </div>
                    @endif

                </div>
                {{-- ===================== END TIMELINE ===================== --}}

            </div>
        </div>
        {{-- ===================== END RIGHT COLUMN ===================== --}}

    </div>
</div>