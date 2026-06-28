{{-- resources/views/livewire/bookings/create.blade.php --}}

<div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

    {{-- ===================== BREADCRUMB ===================== --}}
    <nav class="mb-6 text-sm text-zinc-500 flex items-center gap-2">
        <a href="{{ route('browse.assets') }}"
           wire:navigate
           class="hover:text-zinc-900 dark:hover:text-white transition">
            {{ __('Browse Assets') }}
        </a>
        <span>/</span>
        <a href="{{ route('assets.show', $asset->id) }}"
           wire:navigate
           class="hover:text-zinc-900 dark:hover:text-white transition truncate">
            {{ $asset->title }}
        </a>
        <span>/</span>
        <span class="text-zinc-900 dark:text-white">
            {{ __('Request to Book') }}
        </span>
    </nav>
    {{-- ===================== END BREADCRUMB ===================== --}}


    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- ===================== LEFT: FORM (2 cols) ===================== --}}
        <div class="lg:col-span-2 space-y-6">

            <div>
                <flux:heading size="xl">{{ __('Request to Book') }}</flux:heading>
                <flux:text class="mt-1 text-zinc-500">
                    {{ __('Fill in your rental details. The owner will confirm your request.') }}
                </flux:text>
            </div>

            {{-- ERROR MESSAGE --}}
            @if ($errorMessage)
                <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <flux:text class="text-red-700 dark:text-red-400 text-sm">
                        {{ $errorMessage }}
                    </flux:text>
                </div>
            @endif

            {{-- ===================== ASSET SUMMARY CARD ===================== --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-center gap-4">

                    {{-- Asset thumbnail --}}
                    @if ($asset->media->isNotEmpty())
                        <img
                            src="{{ $asset->media->first()->public_url }}"
                            alt="{{ $asset->title }}"
                            class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                        />
                    @else
                        <div class="w-16 h-16 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                            <span class="text-zinc-400 text-xs">{{ __('No photo') }}</span>
                        </div>
                    @endif

                    {{-- Asset info --}}
                    <div class="min-w-0">
                        <p class="font-semibold text-zinc-900 dark:text-white truncate">
                            {{ $asset->title }}
                        </p>
                        <flux:text class="text-sm text-zinc-500">
                            {{ $asset->asset_type->value ?? $asset->asset_type }}
                            ·
                            {{ $asset->condition->value ?? $asset->condition }}
                        </flux:text>
                        <flux:text class="text-sm text-zinc-500">
                            📍 {{ $asset->region }}
                        </flux:text>
                    </div>

                    {{-- Pricing summary --}}
                    <div class="ml-auto text-right flex-shrink-0">
                        <p class="font-bold text-zinc-900 dark:text-white">
                            {{ number_format($asset->hourly_rate, 0) }}
                            <span class="text-sm font-normal text-zinc-500">ETB/hr</span>
                        </p>
                        <p class="text-sm text-zinc-500">
                            {{ number_format($asset->daily_rate, 0) }} ETB/day
                        </p>
                    </div>

                </div>
            </div>
            {{-- ===================== END ASSET SUMMARY CARD ===================== --}}


            {{-- ===================== BOOKING FORM ===================== --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-6">

                <flux:heading size="lg">{{ __('Rental Period') }}</flux:heading>

                {{-- Date & time inputs --}}
                <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">

                    {{-- Start datetime --}}
                    <div>
                        <flux:input
                            wire:model.live="startDatetime"
                            type="datetime-local"
                            :label="__('Start Date & Time')"
                        />
                        {{--
                            wire:model.live → updates $startDatetime on every change.
                            This triggers updatedStartDatetime() which calls
                            recalculatePricing() so the pricing preview updates
                            the moment the user picks a start time.
                            No submit needed to see the price.
                        --}}
                    </div>

                    {{-- End datetime --}}
                    <div>
                        <flux:input
                            wire:model.live="endDatetime"
                            type="datetime-local"
                            :label="__('End Date & Time')"
                        />
                    </div>

                </div>

                {{-- Duration hint --}}
                @if ($estimatedHours > 0)
                    <div class="flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400">
                        <span>⏱</span>
                        <span>
                            {{ $estimatedHours }} {{ __('hours') }}
                            ({{ __('billed as') }} {{ $estimatedHours }} {{ __('full hours') }})
                        </span>
                    </div>
                @endif

                {{-- ===================== HANDOFF METHOD ===================== --}}
                <div>
                    <flux:select
                        wire:model="handoffMethod"
                        :label="__('How will you collect the asset?')"
                    >
                        {{--
                            $handoffMethods comes from render() in the component.
                            HandoffMethodEnum::cases() returns all enum cases.
                            We iterate them and format the label for display.
                        --}}
                        @foreach ($handoffMethods as $method)
                            <option value="{{ $method->value }}">
                                @switch($method->value)
                                    @case('QR_CODE')
                                        {{ __('QR Code Handoff') }}
                                        @break
                                    @case('MANUAL_KEY')
                                        {{ __('Manual Key Handover') }}
                                        @break
                                    @case('DIGITAL_ACCESS')
                                        {{ __('Digital Access Code') }}
                                        @break
                                    @case('LOCATION_PICKUP')
                                        {{ __('Location Pickup') }}
                                        @break
                                    @default
                                        {{ ucfirst(strtolower(str_replace('_', ' ', $method->value))) }}
                                @endswitch
                            </option>
                        @endforeach
                    </flux:select>
                    {{--
                        Why @switch instead of str_replace?
                        str_replace('_', ' ', 'QR_CODE') → 'QR CODE' — looks wrong.
                        @switch lets us write proper human labels for each case.
                        Easy to extend when new handoff methods are added in Phase 2.
                    --}}
                    @error('handoffMethod')
                        <flux:text class="text-red-500 text-sm mt-1">
                            {{ $message }}
                        </flux:text>
                    @enderror
                </div>
                {{-- ===================== END HANDOFF METHOD ===================== --}}

                {{-- ===================== AVAILABILITY NOTE ===================== --}}
                @if ($asset->available_from || $asset->available_until)
                    <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 p-3">
                        <flux:text class="text-sm text-blue-700 dark:text-blue-400">
                            <span class="font-medium">{{ __('Asset availability:') }}</span>
                            @if ($asset->available_from && $asset->available_until)
                                {{ $asset->available_from->format('M j, Y') }}
                                {{ __('to') }}
                                {{ $asset->available_until->format('M j, Y') }}
                            @elseif ($asset->available_from)
                                {{ __('From') }} {{ $asset->available_from->format('M j, Y') }}
                            @elseif ($asset->available_until)
                                {{ __('Until') }} {{ $asset->available_until->format('M j, Y') }}
                            @endif
                        </flux:text>
                    </div>
                @endif
                {{-- ===================== END AVAILABILITY NOTE ===================== --}}

                {{-- ===================== SUBMIT BUTTON ===================== --}}
                <div class="pt-2">
                    <flux:button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        variant="primary"
                        class="w-full">
                        {{--
                            wire:loading.attr="disabled" → disables the button while
                            submit() is running. Prevents double submissions.
                            Without this, an impatient user could click twice and
                            create two bookings.
                        --}}
                        <span wire:loading.remove wire:target="submit">
                            {{ __('Send Booking Request') }}
                        </span>
                        <span wire:loading wire:target="submit">
                            {{ __('Submitting...') }}
                        </span>
                    </flux:button>

                    <flux:text class="text-xs text-center text-zinc-400 mt-3">
                        {{ __('The owner has 24 hours to confirm or decline your request.') }}
                    </flux:text>
                </div>
                {{-- ===================== END SUBMIT ===================== --}}

            </div>
            {{-- ===================== END BOOKING FORM ===================== --}}

        </div>
        {{-- ===================== END LEFT COLUMN ===================== --}}


        {{-- ===================== RIGHT: PRICING PANEL (1 col) ===================== --}}
        <div class="lg:col-span-1">
            <div class="sticky top-24 space-y-4">

                {{-- ===================== PRICING CARD ===================== --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-6 space-y-4">

                    <flux:heading size="lg">{{ __('Price Summary') }}</flux:heading>

                    @if ($estimatedHours > 0)

                        {{-- Live pricing breakdown --}}
                        <div class="space-y-3 text-sm">

                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>
                                    {{ $estimatedHours }} hrs
                                    × {{ number_format($asset->hourly_rate, 0) }} ETB
                                </span>
                                <span>ETB {{ number_format($estimatedRentalAmount, 2) }}</span>
                            </div>

                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Platform fee (7.5%)') }}</span>
                                <span>ETB {{ number_format($estimatedPlatformFee, 2) }}</span>
                            </div>

                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400 pb-3 border-b border-zinc-100 dark:border-zinc-700">
                                <span>{{ __('Security deposit') }}</span>
                                <span>ETB {{ number_format($asset->security_deposit, 2) }}</span>
                            </div>

                            <div class="flex justify-between font-bold text-zinc-900 dark:text-white text-base">
                                <span>{{ __('Total') }}</span>
                                <span>ETB {{ number_format($estimatedTotal, 2) }}</span>
                            </div>

                        </div>

                        {{-- Deposit note --}}
                        <div class="rounded-lg bg-zinc-50 dark:bg-zinc-800 p-3">
                            <flux:text class="text-xs text-zinc-500">
                                💰 {{ __('The security deposit of') }}
                                <span class="font-medium">
                                    ETB {{ number_format($asset->security_deposit, 2) }}
                                </span>
                                {{ __('is fully refunded after the asset is returned in good condition.') }}
                            </flux:text>
                        </div>

                        {{-- Phase 2 escrow note --}}
                        <flux:text class="text-xs text-zinc-400">
                            {{-- 
                                TODO: Phase 2 — replace this text with real escrow status.
                                Funds will be held in escrow via Chapa/SantimPay until
                                the booking is completed and asset returned.
                            --}}
                            {{ __('Payment collected after owner confirms. Funds held securely until rental completes.') }}
                        </flux:text>

                    @else

                        {{-- Empty state — no dates selected yet --}}
                        <div class="py-6 text-center">
                            <flux:text class="text-zinc-400 text-sm">
                                {{ __('Select your dates to see the price breakdown.') }}
                            </flux:text>
                        </div>

                        {{-- Rate reference even without dates --}}
                        <div class="space-y-2 text-sm border-t border-zinc-100 dark:border-zinc-700 pt-4">
                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Hourly rate') }}</span>
                                <span>ETB {{ number_format($asset->hourly_rate, 0) }}</span>
                            </div>
                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                <span>{{ __('Daily rate') }}</span>
                                <span>ETB {{ number_format($asset->daily_rate, 0) }}</span>
                            </div>
                            @if ($asset->weekly_rate)
                                <div class="flex justify-between text-zinc-600 dark:text-zinc-400">
                                    <span>{{ __('Weekly rate') }}</span>
                                    <span>ETB {{ number_format($asset->weekly_rate, 0) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-zinc-600 dark:text-zinc-400 pt-2 border-t border-zinc-100 dark:border-zinc-700">
                                <span>{{ __('Security deposit') }}</span>
                                <span>ETB {{ number_format($asset->security_deposit, 0) }}</span>
                            </div>
                        </div>

                    @endif

                </div>
                {{-- ===================== END PRICING CARD ===================== --}}


                {{-- ===================== OWNER CARD ===================== --}}
                @if ($asset->owner)
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-3">

                        <flux:heading>{{ __('Asset Owner') }}</flux:heading>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center flex-shrink-0">
                                <span class="text-blue-700 dark:text-blue-300 font-semibold text-sm">
                                    {{ strtoupper(substr($asset->owner->first_name, 0, 1)) }}
                                    {{ strtoupper(substr($asset->owner->last_name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <p class="font-medium text-zinc-900 dark:text-white text-sm">
                                    {{ $asset->owner->first_name }}
                                    {{ $asset->owner->last_name }}
                                </p>
                                @if ($asset->owner->total_trust_score)
                                    <flux:text class="text-xs text-zinc-500">
                                        ⭐ {{ __('Trust Score') }}:
                                        {{ number_format($asset->owner->total_trust_score, 1) }}
                                    </flux:text>
                                @endif
                            </div>
                        </div>

                        <flux:text class="text-xs text-zinc-400">
                            {{ __('Once you submit, the owner will review and confirm your booking request.') }}
                        </flux:text>

                    </div>
                @endif
                {{-- ===================== END OWNER CARD ===================== --}}


                {{-- ===================== CANCELLATION NOTE ===================== --}}
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                    <flux:heading size="sm" class="mb-2">{{ __('Cancellation Policy') }}</flux:heading>
                    <flux:text class="text-xs text-zinc-500">
                        {{ __('You can cancel a PENDING or CONFIRMED booking at any time before the rental begins. Once the handoff is complete and the rental is IN PROGRESS, cancellation is no longer available.') }}
                    </flux:text>
                    {{--
                        TODO: Phase 2 — replace with dynamic cancellation policy
                        based on time before rental start (e.g. full refund if
                        cancelled 24hrs before, 50% refund if cancelled last minute).
                    --}}
                </div>
                {{-- ===================== END CANCELLATION NOTE ===================== --}}

            </div>
        </div>
        {{-- ===================== END RIGHT COLUMN ===================== --}}

    </div>
</div>