<div>
    {{-- Guard: only the renter sees anything in this component --}}
   @if ($this->isRenter)

        @if ($submitted && $submittedReview)
            {{-- ======================================= --}}
            {{-- SUBMITTED STATE: show the review card  --}}
            {{-- ======================================= --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">

                <div class="mb-4 flex items-center justify-between">
                    <flux:heading size="lg">
                        {{ __('Your Review') }}
                    </flux:heading>
                    <flux:text class="text-xs text-zinc-400 dark:text-zinc-500">
                        {{ $submittedReview->created_at->diffForHumans() }}
                    </flux:text>
                </div>

                {{-- Star display (read-only) --}}
                <div class="mb-3 flex items-center gap-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg
                            class="h-5 w-5 {{ $i <= $submittedReview->rating
                                ? 'text-yellow-400'
                                : 'text-zinc-300 dark:text-zinc-600' }}"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor

                    <flux:text class="ml-2 text-sm text-zinc-500 dark:text-zinc-400">
                        {{ $submittedReview->rating }} / 5
                    </flux:text>
                </div>

                <flux:text class="text-sm leading-relaxed text-zinc-700 dark:text-zinc-300">
                    {{ $submittedReview->comment }}
                </flux:text>

                {{-- Owner response — only shown if one exists --}}
                @if ($submittedReview->response_text)
                    <div class="mt-4 rounded-lg bg-zinc-50 dark:bg-zinc-700 p-4">
                        <flux:text class="mb-1 text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">
                            {{ __('Owner\'s Response') }}
                        </flux:text>
                        <flux:text class="text-sm text-zinc-700 dark:text-zinc-300">
                            {{ $submittedReview->response_text }}
                        </flux:text>
                        <flux:text class="mt-1 text-xs text-zinc-400 dark:text-zinc-500">
                            {{ $submittedReview->responded_at?->diffForHumans() }}
                        </flux:text>
                    </div>
                @endif

            </div>

        @else
            {{-- ======================================= --}}
            {{-- FORM STATE: renter has not yet reviewed --}}
            {{-- ======================================= --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-6">

                <flux:heading size="lg" class="mb-1">
                    {{ __('Leave a Review') }}
                </flux:heading>
                <flux:text class="text-zinc-500 dark:text-zinc-400 mb-5">
                    {{ __('Share your experience to help other renters on EthioShare.') }}
                </flux:text>

                <form wire:submit.prevent="submitReview">

                    {{-- ── Star Rating Input ── --}}
                    <div class="mb-5">
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-2">
                            {{ __('Your Rating') }}
                        </flux:text>

                        <div class="flex items-center gap-2">
                            @for ($star = 1; $star <= 5; $star++)
                                <button
                                    type="button"
                                    wire:click="setRating({{ $star }})"
                                    class="focus:outline-none"
                                    aria-label="{{ $star }} {{ $star > 1 ? __('stars') : __('star') }}"
                                >
                                    <svg
                                        class="h-8 w-8 transition-colors duration-100
                                            {{ $star <= $rating
                                                ? 'text-yellow-400'
                                                : 'text-zinc-300 dark:text-zinc-600 hover:text-yellow-300' }}"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            @endfor

                            @if ($rating > 0)
                                <flux:text class="ml-2 text-sm text-zinc-500 dark:text-zinc-400">
                                    {{ $rating }} / 5
                                </flux:text>
                            @endif
                        </div>

                        @error('rating')
                            <flux:text class="mt-1 text-xs text-red-500 dark:text-red-400">
                                {{ $message }}
                            </flux:text>
                        @enderror
                    </div>

                    {{-- ── Comment Textarea ── --}}
                    <div class="mb-5">
                        <flux:text class="text-xs text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-2">
                            {{ __('Your Comment') }}
                        </flux:text>

                        <flux:textarea
                            wire:model="comment"
                            id="review-comment"
                            rows="4"
                            :placeholder="__('Describe your experience with this asset and owner...')"
                            class="{{ $errors->has('comment') ? 'ring-1 ring-red-400 border-red-400' : '' }}"
                        />

                        <div class="mt-1 flex items-start justify-between">
                            @error('comment')
                                <flux:text class="text-xs text-red-500 dark:text-red-400">
                                    {{ $message }}
                                </flux:text>
                            @else
                                <span></span>
                            @enderror

                            <flux:text class="text-xs text-zinc-400 dark:text-zinc-500">
                                {{ strlen($comment) }} / 2000
                            </flux:text>
                        </div>
                    </div>

                    {{-- ── Action-level error (from SubmitReviewAction guards) ── --}}
                    @if ($errorMessage)
                        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3">
                            <flux:text class="text-sm text-red-700 dark:text-red-400">
                                {{ $errorMessage }}
                            </flux:text>
                        </div>
                    @endif

                    {{-- ── Submit Button ── --}}
                    <flux:button
                        type="submit"
                        variant="primary"
                        wire:loading.attr="disabled"
                        wire:target="submitReview"
                        class="w-full"
                    >
                        <span wire:loading.remove wire:target="submitReview">
                            ✓ {{ __('Submit Review') }}
                        </span>
                        <span wire:loading wire:target="submitReview">
                            {{ __('Submitting...') }}
                        </span>
                    </flux:button>

                </form>
            </div>

        @endif

    @endif
</div>