<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-900">

        {{-- ===================== TOP NAVBAR ===================== --}}
        <nav class="sticky top-0 z-50 border-b border-zinc-200 bg-zinc-50 backdrop-blur-sm dark:border-zinc-700 dark:bg-zinc-900">

            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <div class="flex h-16 items-center justify-between">

                    {{-- LEFT: Logo --}}
                    <div class="flex items-center">
                        <a href="{{ route('browse.assets') }}" wire:navigate
                           class="flex items-center gap-2 text-xl font-bold text-zinc-900 dark:text-white">
                            <x-app-logo />
                        </a>
                    </div>

                    {{-- CENTER: Navigation links --}}
                    <div class="hidden md:flex items-center gap-6">
                        <a href="{{ route('browse.assets') }}"
                           wire:navigate
                           class="text-sm font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
                            {{ __('Browse Assets') }}
                        </a>
                    </div>

                    {{-- RIGHT: Auth buttons or user menu --}}
                    <div class="flex items-center gap-3">
                        @auth
                            <flux:button
                                href="{{ route('dashboard') }}"
                                wire:navigate
                                variant="ghost"
                                size="sm">
                                {{ __('Dashboard') }}
                            </flux:button>
                        @else
                            <flux:button
                                href="{{ route('login') }}"
                                wire:navigate
                                variant="ghost"
                                size="sm">
                                {{ __('Log in') }}
                            </flux:button>

                            <flux:button
                                href="{{ route('register') }}"
                                wire:navigate
                                variant="primary"
                                size="sm">
                                {{ __('Sign up') }}
                            </flux:button>
                        @endauth
                    </div>

                </div>
            </div>
        </nav>
        {{-- ===================== END NAVBAR ===================== --}}


        {{-- ===================== PAGE CONTENT ===================== --}}
        <main>
            {{ $slot }}
        </main>
        {{-- ===================== END PAGE CONTENT ===================== --}}


        {{-- ===================== FOOTER ===================== --}}
        <footer class="mt-auto border-t border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                    <p class="text-sm text-zinc-500 dark:text-zinc-400">
                        © {{ date('Y') }} EthioShare. {{ __('Ethiopia\'s Asset Sharing Marketplace.') }}
                    </p>

                    <div class="flex items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                        <span>{{ __('Built for Ethiopia') }}</span>
                    </div>
                </div>
            </div>
        </footer>
        {{-- ===================== END FOOTER ===================== --}}


        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist
        {{--
            @persist('toast') → Livewire keeps this element alive across page navigations.
            Without @persist, toast notifications disappear when wire:navigate triggers.
            This is why it exists in your app.blade.php too — same reason.
        --}}

        @fluxScripts
        {{--
            Required by Flux UI. Must be at the bottom of every layout.
            Loads Flux's Alpine.js interactions.
            Without this, Flux dropdowns, modals, and toggles won't work.
        --}}

    </body>
</html>