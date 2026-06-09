<div>
    {{-- Every Livewire component view must have ONE root element --}}
    {{-- This div is that root element --}}

    {{-- ===================== HERO SECTION ===================== --}}
    <section class="bg-gradient-to-br from-zinc-900 to-zinc-800 py-20 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-5xl">
                    {{ __('Rent Any Asset in Ethiopia') }}
                </h1>
                <p class="mt-4 text-lg text-zinc-300">
                    {{ __('Find machinery, vehicles, equipment and more across Ethiopian regions.') }}
                </p>

                {{-- Simple search bar placeholder --}}
                <div class="mt-8 flex justify-center">
                    <flux:button
                        href="{{ route('browse.assets') }}"
                        wire:navigate
                        variant="primary">
                        {{ __('Browse Assets') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </section>
    {{-- ===================== END HERO ===================== --}}


    {{-- ===================== COMING SOON PLACEHOLDER ===================== --}}
    <section class="py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <flux:heading size="xl">
                    {{ __('Assets coming soon') }}
                </flux:heading>
                <flux:text class="mt-2 text-zinc-500">
                    {{ __('Asset listings will appear here once we connect the backend.') }}
                </flux:text>
            </div>
        </div>
    </section>
    {{-- ===================== END PLACEHOLDER ===================== --}}

</div>