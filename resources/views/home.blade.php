@extends('layouts.guest')

@section('content')
    {{-- BACKGROUND DECORATIONS (Premium Ambient Aura Glow) --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-[700px] pointer-events-none overflow-hidden opacity-30 dark:opacity-20 z-0">
        <div class="absolute top-[-10%] left-[10%] w-[600px] h-[600px] rounded-full bg-gradient-to-tr from-amber-400/80 to-emerald-500/80 blur-[160px]"></div>
        <div class="absolute top-[20%] right-[10%] w-[500px] h-[500px] rounded-full bg-gradient-to-br from-emerald-400/80 to-cyan-500/80 blur-[140px]"></div>
    </div>

    {{-- HERO SECTION --}}
    <section class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-32 pb-24 text-center z-10">
        {{-- Micro Badge --}}
        <div class="inline-flex items-center gap-2.5 px-4 py-2 text-xs sm:text-sm font-bold rounded-full bg-white dark:bg-zinc-900/80 text-zinc-800 dark:text-zinc-200 border border-zinc-200 dark:border-zinc-700 mb-10 shadow-sm backdrop-blur-md">
            <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Ethiopia's Premier Equipment Marketplace</span>
        </div>

        <flux:heading class="text-5xl sm:text-6xl md:text-8xl font-black tracking-tight max-w-5xl mx-auto leading-tight text-zinc-900 dark:text-white">
            Share Equipment, <br class="hidden sm:inline" />
            <span class="bg-gradient-to-r from-amber-500 via-emerald-500 to-cyan-500 bg-clip-text text-transparent drop-shadow-sm">Build Ethiopia</span>
        </flux:heading>

        <flux:text class="max-w-3xl mx-auto mt-8 text-lg sm:text-xl md:text-2xl text-zinc-600 dark:text-zinc-300 font-medium leading-relaxed">
            Rent high-quality construction, agricultural, and industrial machinery from trusted verified owners nationwide with absolute zero platform friction.
        </flux:text>

        <div class="mt-12 flex flex-col sm:flex-row justify-center items-center gap-6 max-w-md mx-auto sm:max-w-none">
            <flux:button variant="primary" class="w-full sm:w-auto px-10 py-4 text-base rounded-xl font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-xl shadow-emerald-600/20 dark:shadow-none transition-all duration-300 hover:scale-[1.02]">
                Find Equipment
            </flux:button>

            <flux:button variant="outline" class="w-full sm:w-auto px-10 py-4 text-base rounded-xl font-bold border-zinc-300 dark:border-zinc-700 bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md transition-all duration-300 hover:scale-[1.02] hover:bg-zinc-50 dark:hover:bg-zinc-800">
                List Your Equipment
            </flux:button>
        </div>

        <flux:text class="mt-12 text-sm font-bold tracking-widest uppercase text-zinc-500 dark:text-zinc-400 block">
            Trusted by 500+ Ethiopian Construction & Agro Partners
        </flux:text>
    </section>

    {{-- METRICS & STATS SECTION --}}
    <section class="relative max-w-7xl mx-auto px-4 sm:px-6 py-12 z-10">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            @foreach([
                ['500+', 'Active Listings', 'border-amber-500/30'],
                ['1,000+', 'Happy Renters', 'border-emerald-500/30'],
                ['50+', 'Machinery Scales', 'border-blue-500/30'],
                ['98%', 'Success Metric', 'border-purple-500/30']
            ] as $stat)
                <flux:card class="relative overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-lg p-8 rounded-3xl text-center transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl">
                    <flux:heading class="text-4xl sm:text-5xl font-black tracking-tight text-zinc-900 dark:text-white mb-2">{{ $stat[0] }}</flux:heading>
                    <flux:text class="text-sm font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400 block">{{ $stat[1] }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how" class="max-w-7xl mx-auto px-4 sm:px-6 py-28 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-20">
            <h2 class="text-sm font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400 mb-3">OPERATIONAL WORKFLOW</h2>
            <flux:heading class="text-4xl sm:text-5xl font-black text-zinc-900 dark:text-white">How It Works</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            @foreach([
                ['Create Account', 'Sign up as an asset owner or general renter securely in under two minutes.', '📝'],
                ['Find & Book', 'Filter catalog parameters to pinpoint machinery requirements and reserve instant blocks.', '🔍'],
                ['Rent & Return', 'Coordinate localized handoff protocols, complete operations, and secure safe returns.', '🤝']
            ] as $index => $step)
                <flux:card class="relative border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-10 rounded-3xl transition-all duration-300 hover:shadow-2xl group">
                    <div class="absolute top-6 right-8 text-6xl font-black text-zinc-100 dark:text-zinc-800/50 select-none group-hover:text-emerald-50 dark:group-hover:text-emerald-900/20 transition-colors duration-300">
                        0{{ $index + 1 }}
                    </div>
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-zinc-100 dark:bg-zinc-800 text-3xl shadow-inner mb-8 border border-zinc-200/50 dark:border-zinc-700">
                        {{ $step[2] }}
                    </div>
                    <flux:heading class="font-bold text-2xl text-zinc-900 dark:text-white mb-3">{{ $step[0] }}</flux:heading>
                    <flux:text class="text-base text-zinc-600 dark:text-zinc-400 leading-relaxed block">{{ $step[1] }}</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- CATEGORIES TAXONOMY SECTION --}}
    <section id="categories" class="max-w-7xl mx-auto px-4 sm:px-6 py-16 relative z-10 bg-zinc-50 dark:bg-zinc-900/30 rounded-3xl mb-20 border border-zinc-200/50 dark:border-zinc-800/50">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-sm font-black uppercase tracking-widest text-amber-600 dark:text-amber-500 mb-3">TAXONOMY METRICS</h2>
            <flux:heading class="text-4xl sm:text-5xl font-black text-zinc-900 dark:text-white">Browse By Category</flux:heading>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
            @foreach([
                'Construction Equipment', 'Agricultural Machinery', 'Commercial Vehicles',
                'Power Tools', 'Event Infrastructure', 'Industrial Frameworks', 'Office Equipment',
                'Technology Hardware', 'Safety Architectures', 'Landscaping Systems'
            ] as $cat)
                <flux:card class="p-6 text-center border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 rounded-2xl cursor-pointer transition-all duration-300 hover:border-emerald-500 dark:hover:border-emerald-500 hover:shadow-xl hover:-translate-y-1 group">
                    <flux:text class="font-bold text-base text-zinc-800 dark:text-zinc-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors block">{{ $cat }}</flux:text>
                    <flux:text class="text-sm font-semibold text-zinc-400 dark:text-zinc-500 mt-2 block">(24 items)</flux:text>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- FEATURED LISTINGS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-20 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-sm font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-500 mb-3">MARKETPLACE INDEX</h2>
            <flux:heading class="text-4xl sm:text-5xl font-black text-zinc-900 dark:text-white">Featured Listings</flux:heading>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach(range(1, 4) as $i)
                <flux:card class="overflow-hidden border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-3xl transition-all duration-300 hover:-translate-y-2 hover:shadow-2xl group flex flex-col justify-between">
                    <div>
                        {{-- Clean Dashboard Asset Graphic Placeholding Matrix --}}
                        <div class="relative h-56 bg-zinc-100 dark:bg-zinc-950 overflow-hidden flex items-center justify-center border-b border-zinc-200 dark:border-zinc-800">
                            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-amber-500/10 z-0"></div>
                            <span class="text-6xl group-hover:scale-125 transition-transform duration-500 z-10">🏗️</span>
                            <div class="absolute top-4 right-4 bg-white/95 dark:bg-zinc-900/95 backdrop-blur px-3 py-1.5 rounded-lg text-xs font-black uppercase tracking-wider text-emerald-600 dark:text-emerald-400 shadow-lg border border-emerald-500/20">
                                Active Available
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <flux:heading class="font-black text-lg tracking-tight text-zinc-900 dark:text-white">CAT 320D Excavator</flux:heading>
                            
                            <div class="mt-3 flex items-baseline gap-1.5">
                                <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">1,500</span>
                                <span class="text-sm font-bold text-zinc-500 dark:text-zinc-400">ETB / day</span>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-4 text-zinc-600 dark:text-zinc-400">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                                <span class="text-sm font-semibold">Addis Ababa, Bole</span>
                            </div>

                            <div class="flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800 mt-6 pt-4">
                                <div class="flex items-center gap-1.5 font-bold text-zinc-800 dark:text-zinc-200 text-sm">
                                    <span class="text-amber-500 text-lg">★</span>
                                    <span>4.8</span>
                                    <span class="text-xs text-zinc-400 font-medium ml-1">(12 Reviews)</span>
                                </div>
                                <span class="text-xs px-2.5 py-1 font-bold rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 uppercase tracking-widest">Heavy</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 pt-0">
                        <flux:button variant="outline" class="w-full py-3 rounded-xl text-sm font-bold transition-all duration-300 group-hover:bg-emerald-600 group-hover:text-white group-hover:border-emerald-600">
                            View Parameters
                        </flux:button>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- VALUE PROPOSITION ARRAYS --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-20 relative z-10 border-t border-zinc-200/60 dark:border-zinc-800/60">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h2 class="text-sm font-black uppercase tracking-widest text-zinc-500 dark:text-zinc-400 mb-3">VALUE PROPOSITION</h2>
            <flux:heading class="text-4xl sm:text-5xl font-black text-zinc-900 dark:text-white">Why Choose EthioShare</flux:heading>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach([
                ['Verified Owners', 'All framework providers pass multi-tier operational tracking protocols.', '🛡️'],
                ['Secure Transactions', 'Escrow parameter layers shield digital asset deposits uniformly.', '💳'],
                ['Flexible Rentals', 'Rent natively by customized hour shifts, full weeks, or annual schedules.', '⏳'],
                ['Regional Continuity', 'Connect seamlessly across localized regions without transport deadlocks.', '🌍'],
                ['24/7 Local Support', 'Direct premier customer support desks active through local channels.', '🗣️'],
                ['Transparent Rates', 'Peer indexing alignment engine prevents unnecessary premium markups.', '🏷️']
            ] as $feature)
                <flux:card class="flex items-start gap-5 p-8 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 rounded-3xl transition-all duration-300 hover:shadow-xl hover:border-emerald-500/50">
                    <div class="flex-shrink-0 text-3xl p-4 rounded-2xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200/50 dark:border-zinc-700">{{ $feature[2] }}</div>
                    <div>
                        <flux:heading class="font-bold text-xl text-zinc-900 dark:text-white mb-2">{{ $feature[0] }}</flux:heading>
                        <flux:text class="text-base text-zinc-600 dark:text-zinc-400 leading-relaxed block">{{ $feature[1] }}</flux:text>
                    </div>
                </flux:card>
            @endforeach
        </div>
    </section>

    {{-- TRUST BADGES ROW --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-12 text-center relative z-10">
        <div class="flex flex-wrap justify-center gap-3">
            @foreach([
                '✓ Phone Verified Users', '★ Real Business Reviews', '🇪🇹 100% Ethiopian Owned',
                '📍 Localized Hub Operations', '🔒 Advanced Privacy', '🛡️ Fair Escrow Policy'
            ] as $badge)
                <span class="px-4 py-2 rounded-full text-sm font-bold border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 shadow-sm block">
                    {{ $badge }}
                </span>
            @endforeach
        </div>
    </section>

    {{-- CALL TO ACTION INTERACTIVE MAP OVERLAY LAYER --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 my-24 relative z-10">
        <div class="relative bg-zinc-900 dark:bg-zinc-950 py-20 px-6 md:px-16 rounded-[2.5rem] overflow-hidden shadow-2xl text-center border border-zinc-800">
            <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-emerald-500/20 blur-[100px] rounded-full"></div>
            <div class="absolute -left-20 -top-20 w-96 h-96 bg-amber-500/10 blur-[100px] rounded-full"></div>
            
            <div class="relative max-w-3xl mx-auto">
                <flux:heading class="text-white text-4xl sm:text-6xl font-black tracking-tight mb-6">
                    Ready to Start Sharing?
                </flux:heading>
                <flux:text class="text-zinc-400 text-lg sm:text-xl mb-10 leading-relaxed font-medium block">
                    Join Ethiopia's high-speed industrial sharing market today. Scale resource logistics up, or transform downtime machinery columns into steady revenue streams.
                </flux:text>
                <div class="flex flex-col sm:flex-row justify-center gap-4 max-w-md mx-auto sm:max-w-none">
                    <flux:button variant="primary" class="w-full sm:w-auto bg-emerald-500 text-white hover:bg-emerald-400 font-black px-10 py-4 text-base rounded-xl shadow-lg shadow-emerald-500/20 transition-transform active:scale-95">
                        Get Started Now
                    </flux:button>
                    <flux:button variant="outline" class="w-full sm:w-auto border-zinc-700 text-white hover:bg-white/10 font-bold px-10 py-4 text-base rounded-xl transition-transform active:scale-95">
                        Learn More
                    </flux:button>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER STRUCTURE --}}
    <footer class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 py-20 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-10 lg:gap-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                        <flux:heading class="font-black text-xl tracking-tight text-zinc-900 dark:text-white">EthioShare</flux:heading>
                    </div>
                    <flux:text class="text-zinc-600 dark:text-zinc-400 text-sm leading-relaxed block">
                        Fostering asset-light development workflows. Share equipment, build modern regional logistics infrastructure.
                    </flux:text>
                </div>
                <div>
                    <flux:heading class="font-black text-sm tracking-wider uppercase text-zinc-900 dark:text-zinc-200">Quick Portal</flux:heading>
                    <div class="mt-6 space-y-3 font-semibold text-zinc-600 dark:text-zinc-400 text-sm">
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">How It Works</a>
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Browse Equipment</a>
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">List Machinery</a>
                    </div>
                </div>
                <div>
                    <flux:heading class="font-black text-sm tracking-wider uppercase text-zinc-900 dark:text-zinc-200">Support Hub</flux:heading>
                    <div class="mt-6 space-y-3 font-semibold text-zinc-600 dark:text-zinc-400 text-sm">
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Help Center</a>
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Safety Standards</a>
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">FAQ Matrix</a>
                    </div>
                </div>
                <div>
                    <flux:heading class="font-black text-sm tracking-wider uppercase text-zinc-900 dark:text-zinc-200">Legal Systems</flux:heading>
                    <div class="mt-6 space-y-3 font-semibold text-zinc-600 dark:text-zinc-400 text-sm">
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Terms of Service</a>
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Privacy Protections</a>
                        <a href="#" class="block hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Refund Mechanisms</a>
                    </div>
                </div>
                <div class="col-span-2 md:col-span-1">
                    <flux:heading class="font-black text-sm tracking-wider uppercase text-zinc-900 dark:text-zinc-200">Contact Nodes</flux:heading>
                    <div class="mt-6 space-y-3 text-zinc-600 dark:text-zinc-400 text-sm font-medium">
                        <span class="block font-bold text-emerald-600 dark:text-emerald-500">hello@ethioshare.et</span>
                        <span class="block">+251 11 XXX XXXX</span>
                        <span class="block">Addis Ababa, Ethiopia</span>
                    </div>
                </div>
            </div>
            <div class="mt-16 pt-8 border-t border-zinc-200 dark:border-zinc-800 text-center text-sm text-zinc-500 dark:text-zinc-400 font-semibold">
                © {{ date('Y') }} EthioShare. Fostering continuous circular efficiency models. All rights reserved.
            </div>
        </div>
    </footer>
@endsection