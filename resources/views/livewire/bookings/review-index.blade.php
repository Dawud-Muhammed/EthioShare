<div class="max-w-5xl mx-auto py-8 px-4">

    {{-- ===================== HEADER ===================== --}}
    <div class="mb-6">
        <flux:heading size="xl">{{ __('Reviews') }}</flux:heading>
        <flux:text class="text-zinc-500 dark:text-zinc-400 mt-1">
            {{ __('Reviews you have written and reviews your assets have received.') }}
        </flux:text>
    </div>
    {{-- ===================== END HEADER ===================== --}}


    {{-- ===================== TABS ===================== --}}
    <div class="mb-6 flex items-center gap-1 border-b border-zinc-200 dark:border-zinc-700">

        {{-- GIVEN TAB --}}
        <button
            wire:click="setTab('given')"
            type="button"
            class="px-4 py-2.5 text-sm font-medium transition border-b-2 -mb-px
                {{ $activeTab === 'given'
                    ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                    : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
        >
            {{ __('Reviews Given') }}
        </button>

        {{-- RECEIVED TAB --}}
        <button
            wire:click="setTab('received')"
            type="button"
            class="px-4 py-2.5 text-sm font-medium transition border-b-2 -mb-px
                {{ $activeTab === 'received'
                    ? 'border-zinc-900 dark:border-white text-zinc-900 dark:text-white'
                    : 'border-transparent text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}"
        >
            {{ __('Reviews Received') }}
        </button>

    </div>
    {{-- ===================== END TABS ===================== --}}


    {{-- ===================== REVIEW LIST ===================== --}}
    @if ($this->reviews->isEmpty())

        {{-- EMPTY STATE --}}
        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-12 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-700">
                <svg class="h-6 w-6 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                </svg>
            </div>

            @if ($activeTab === 'given')
                <flux:heading size="lg" class="mb-1">
                    {{ __('No reviews written yet') }}
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400 mb-4">
                    {{ __('Complete a booking to leave your first review.') }}
                </flux:text>
                <flux:button
                    :href="route('bookings.index')"
                    wire:navigate
                    variant="primary"
                >
                    {{ __('View My Bookings') }}
                </flux:button>
            @else
                <flux:heading size="lg" class="mb-1">
                    {{ __('No reviews received yet') }}
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400 mb-4">
                    {{ __('Reviews will appear here once renters complete their bookings.') }}
                </flux:text>
                <flux:button
                    :href="route('assets.index')"
                    wire:navigate
                    variant="primary"
                >
                    {{ __('View My Assets') }}
                </flux:button>
            @endif
        </div>

    @else

        <div class="space-y-4">
            @foreach ($this->reviews as $review)

                <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">

                    {{-- CARD HEADER: asset title + date --}}
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            {{-- Asset name — reviewable is the Asset model --}}
                            <flux:heading size="sm" class="text-zinc-900 dark:text-white">
                                {{ $review->reviewable?->title ?? __('Asset no longer available') }}
                            </flux:heading>

                            {{-- On "received" tab, show who wrote the review --}}
                            @if ($activeTab === 'received')
                                <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ __('By') }}
                                    {{ $review->reviewer?->first_name }}
                                    {{ $review->reviewer?->last_name }}
                                </flux:text>
                            @endif
                        </div>

                        <flux:text class="text-xs text-zinc-400 dark:text-zinc-500 flex-shrink-0">
                            {{ $review->created_at->diffForHumans() }}
                        </flux:text>
                    </div>

                    {{-- STAR DISPLAY (read-only) --}}
                    <div class="mb-3 flex items-center gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg
                                class="h-4 w-4 {{ $i <= $review->rating
                                    ? 'text-yellow-400'
                                    : 'text-zinc-300 dark:text-zinc-600' }}"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                            >
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                        <flux:text class="ml-1.5 text-xs text-zinc-500 dark:text-zinc-400">
                            {{ $review->rating }} / 5
                        </flux:text>

                        @if ($review->is_verified_booking)
                            <span class="ml-2 inline-flex items-center gap-1 rounded-full bg-green-100 dark:bg-green-900/30 px-2 py-0.5 text-xs font-medium text-green-700 dark:text-green-400">
                                ✓ {{ __('Verified') }}
                            </span>
                        @endif
                    </div>

                    {{-- COMMENT --}}
                    <flux:text class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                        {{ $review->comment }}
                    </flux:text>

                    {{-- OWNER RESPONSE — shown on "given" tab if owner replied --}}
                    @if ($review->response_text)
                        <div class="mt-4 rounded-lg bg-zinc-50 dark:bg-zinc-700 p-4">
                            <flux:text class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-1">
                                {{ __('Owner\'s Response') }}
                            </flux:text>
                            <flux:text class="text-sm text-zinc-700 dark:text-zinc-300">
                                {{ $review->response_text }}
                            </flux:text>
                            <flux:text class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                                {{ $review->responded_at?->diffForHumans() }}
                            </flux:text>
                        </div>
                    @endif

                </div>

            @endforeach
        </div>

        {{-- PAGINATION --}}
        <div class="mt-6">
            {{ $this->reviews->links() }}
        </div>

    @endif
    {{-- ===================== END REVIEW LIST ===================== --}}

</div>