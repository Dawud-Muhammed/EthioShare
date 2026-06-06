<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">
<head>
    @include('partials.head')
    @fluxAppearance
    <style>
        /* Smooth rendering optimizations */
        body {
            font-feature-settings: "cv02", "cv03", "cv04", "cv11";
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
        }
    </style>
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 antialiased selection:bg-emerald-500 selection:text-white">
    
    {{-- Global Navigation Bar --}}
    <nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-xl px-4 sm:px-8 py-4 transition-all duration-300 shadow-sm">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            
            {{-- Brand Logo (Upgraded with Icon & Spacing) --}}
            <a href="/" class="flex items-center gap-2.5 group hover:opacity-90 transition-opacity focus:outline-none">
                <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6 sm:w-7 sm:h-7 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <div class="font-black text-xl sm:text-2xl tracking-tight text-zinc-900 dark:text-white">
                    Ethio<span class="text-emerald-600 dark:text-emerald-400">Share</span>
                </div>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex gap-8 lg:gap-10 items-center text-sm font-medium text-zinc-600 dark:text-zinc-300">
                <a href="#" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">Home</a>
                <a href="#how" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">How It Works</a>
                <a href="#categories" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">Categories</a>
                <a href="#" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">About</a>
                
                @auth
                    <a href="#" class="text-emerald-600 dark:text-emerald-400 font-bold hover:opacity-80 transition-opacity flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Rent Your Asset
                    </a>
                @endauth

                <span class="h-5 w-px bg-zinc-300 dark:bg-zinc-700 mx-2"></span>
                
                @guest
                    <a href="{{ route('login') }}" class="font-semibold hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200">Login</a>
                    <a href="{{ route('register') }}" class="shadow-md font-bold tracking-wide bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2.5 rounded-xl text-sm transition-transform duration-200 active:scale-95">Register</a>
                @else
                    <a href="{{ route('dashboard') }}" class="font-bold text-zinc-900 dark:text-white hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="hover:text-red-500 dark:hover:text-red-400 transition-colors text-xs font-bold uppercase tracking-wider pl-2">Logout</button>
                    </form>
                @endguest
            </div>

            {{-- Mobile Menu Trigger --}}
            <button class="md:hidden p-2 rounded-xl text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none transition-colors" @click="open = !open" aria-label="Toggle Menu">
                <svg class="w-6 h-6 transform transition-transform duration-200" :class="{'rotate-90': open}" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" x-show="!open"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" x-show="open" style="display: none;"></path>
                </svg>
            </button>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             @click.away="open = false" 
             class="md:hidden mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 space-y-2 text-base font-medium text-zinc-700 dark:text-zinc-300" 
             style="display: none;">
            
            <a href="#" class="block py-2.5 px-4 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Home</a>
            <a href="#how" @click="open = false" class="block py-2.5 px-4 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">How It Works</a>
            <a href="#categories" @click="open = false" class="block py-2.5 px-4 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Categories</a>
            
            @auth
                <a href="#" class="block py-2.5 px-4 rounded-xl text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 font-bold mt-2">Rent Your Asset</a>
            @endauth

            <div class="h-px bg-zinc-200 dark:bg-zinc-800 my-4"></div>
            
            @guest
                <a href="{{ route('login') }}" class="block py-2.5 px-4 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 font-semibold">Login</a>
                <a href="{{ route('register') }}" class="block text-center mt-3 bg-emerald-600 hover:bg-emerald-500 text-white py-3 rounded-xl font-bold text-sm transition-transform active:scale-95 shadow-md">Register</a>
            @else
                <a href="{{ route('dashboard') }}" class="block py-2.5 px-4 rounded-xl text-zinc-900 dark:text-white font-bold bg-zinc-100 dark:bg-zinc-800">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="block w-full mt-2">
                    @csrf
                    <button type="submit" class="block py-2.5 px-4 w-full text-left rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold transition-colors">Logout</button>
                </form>
            @endguest
        </div>
    </nav>

    {{-- Main App View Frame Layer --}}
    <main class="relative min-h-[calc(100vh-80px)] z-10 overflow-x-hidden">
        @yield('content')
    </main>

    {{-- System Livewire Toast Persistence Layer Framework --}}
    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>
</html>