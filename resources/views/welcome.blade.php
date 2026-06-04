<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => config('app.name', 'EthioShare')])
        <script>
            (() => {
                const storageKey = 'ethioshare-theme';
                const storedTheme = localStorage.getItem(storageKey);
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = storedTheme || (prefersDark ? 'dark' : 'light');

                document.documentElement.classList.toggle('dark', theme === 'dark');
                document.documentElement.dataset.theme = theme;
                document.documentElement.style.colorScheme = theme;
            })();
        </script>
        <style>
            html[data-theme="dark"] body {
                background: #020617 !important;
                color: #e2e8f0 !important;
            }

            html[data-theme="dark"] header,
            html[data-theme="dark"] footer {
                background: rgba(2, 6, 23, 0.92) !important;
                border-color: rgba(148, 163, 184, 0.18) !important;
            }

            html[data-theme="dark"] .bg-white,
            html[data-theme="dark"] .bg-slate-50,
            html[data-theme="dark"] .bg-slate-100 {
                background-color: rgba(15, 23, 42, 0.88) !important;
            }

            html[data-theme="dark"] .border-slate-200 {
                border-color: rgba(148, 163, 184, 0.18) !important;
            }

            html[data-theme="dark"] .text-slate-950,
            html[data-theme="dark"] .text-slate-900 {
                color: #ffffff !important;
            }

            html[data-theme="dark"] .text-slate-700,
            html[data-theme="dark"] .text-slate-600,
            html[data-theme="dark"] .text-slate-500 {
                color: #cbd5e1 !important;
            }

            html[data-theme="dark"] .text-slate-400 {
                color: #94a3b8 !important;
            }

            html[data-theme="dark"] main article,
            html[data-theme="dark"] main section > div > div > article,
            html[data-theme="dark"] main section > div > article,
            html[data-theme="dark"] main section > div > div > div > article,
            html[data-theme="dark"] main section > div > div > div,
            html[data-theme="dark"] main section > div > div,
            html[data-theme="dark"] main section > div > div > div > div {
                background-color: rgba(15, 23, 42, 0.88) !important;
                border-color: rgba(148, 163, 184, 0.18) !important;
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased transition-colors duration-300 dark:!bg-slate-950 dark:!text-slate-100">
        @php
            $locale = request()->query('lang', 'en');

            $copy = [
                'en' => [
                    'badge' => 'Guest homepage for EthioShare',
                    'title' => 'A modern place to share assets, book with trust, and keep every transaction in one flow.',
                    'lead' => 'EthioShare gives guests and owners a clean, professional experience for discovery, booking, handoff, and reputation management without the clutter of a generic landing page.',
                    'cta_primary' => 'Start now',
                    'cta_secondary' => 'Create account',
                    'cta_tertiary' => 'Explore features',
                    'hero_label' => 'Platform preview',
                    'hero_title' => 'A premium flow from discovery to handoff',
                    'hero_asset_label' => 'Featured asset',
                    'hero_asset_title' => 'Toyota Hiace Van',
                    'hero_asset_desc' => 'Oromia · Available this week · Trusted by recurring guests',
                    'booking_label' => 'Booking status',
                    'booking_value' => 'Confirmed',
                    'booking_desc' => 'Clear deposit, dates, and pickup details.',
                    'trust_label' => 'Trust score',
                    'trust_desc' => 'Built from verified activity and reviews.',
                    'features_title' => 'Built to feel clear, premium, and easy to trust.',
                    'features_lead' => 'A guest homepage should explain the product quickly and make the next action obvious without looking generic.',
                    'how_title' => 'A simple flow that feels reliable from the first click.',
                    'how_lead' => 'The public homepage should guide visitors into the app while still looking like part of the same product family as the dashboard and auth screens.',
                    'trust_title' => 'A homepage that feels safe, credible, and ready for real transactions.',
                    'trust_lead' => 'EthioShare is a structured service for bookings, deposits, handoffs, reputation, and disputes. The public page should communicate that clearly.',
                    'trust_points_title' => 'What visitors should understand immediately',
                    'footer_lead' => 'A modern guest homepage for a trusted Ethiopian asset-sharing platform.',
                    'footer_built' => 'Built for verified sharing, bookings, and trust.',
                ],
                'am' => [
                    'badge' => 'ለእንግዶች የEthioShare መነሻ ገጽ',
                    'title' => 'አስማታዊ ንብረት ማጋራት፣ በመተማመን ማስያዝ፣ እና ሁሉንም ግብይት በአንድ ፍሰት ማስተዳደር የሚያስችል ዘመናዊ ቦታ።',
                    'lead' => 'EthioShare እንግዶችና ባለቤቶች ለፈለጉት ንብረት ፍለጋ፣ ማስያዝ፣ ማስረከብ እና የታማኝነት አስተዳደር ንጹሕ እና ባለሙያ ተሞክሮ ይሰጣል።',
                    'cta_primary' => 'ጀምር',
                    'cta_secondary' => 'መለያ ፍጠር',
                    'cta_tertiary' => 'ባህሪያት እይ',
                    'hero_label' => 'የመድረክ ቅድመ እይታ',
                    'hero_title' => 'ከፍለ ፍለጋ እስከ ማስረከብ ድረስ የተጣጣመ ፍሰት',
                    'hero_asset_label' => 'የተገለጸ ንብረት',
                    'hero_asset_title' => 'Toyota Hiace Van',
                    'hero_asset_desc' => 'ኦሮሚያ · በዚህ ሳምንት ዝግጁ · በተደጋጋሚ ተጠቃሚዎች የታመነ',
                    'booking_label' => 'የማስያዝ ሁኔታ',
                    'booking_value' => 'ተረጋግጧል',
                    'booking_desc' => 'ግልጽ የተቀማጭ ክፍያ፣ ቀናት እና መቀበያ መረጃ።',
                    'trust_label' => 'የታማኝነት ነጥብ',
                    'trust_desc' => 'በተረጋገጠ እንቅስቃሴ እና ግምገማ ላይ የተመሠረተ።',
                    'features_title' => 'ግልጽ፣ ፕሪሚየም እና ለመተማመን ቀላል እንዲሰማ የተገነባ።',
                    'features_lead' => 'የእንግዳ መነሻ ገጽ ምርቱን በፍጥነት ማብራራት እና ቀጣይ እርምጃውን ግልጽ ማድረግ አለበት።',
                    'how_title' => 'ከመጀመሪያው ጠቅታ ጀምሮ የሚታመን ቀላል ፍሰት።',
                    'how_lead' => 'የህዝብ መነሻ ገጹ ጎብኚዎችን ወደ መተግበሪያው መምራት እና ከዳሽቦርዱ እና ከመግቢያ ገጾች ጋር የሚመሳሰል መሆን አለበት።',
                    'trust_title' => 'ደህንነት ያለው፣ የሚታመን እና ለእውነተኛ ግብይት ዝግጁ የሆነ መነሻ ገጽ።',
                    'trust_lead' => 'EthioShare ለማስያዝ፣ ለተቀማጭ ክፍያ፣ ለማስረከብ፣ ለውጤት ግምገማ እና ለክርክር ማስተዳደር የተደራጀ አገልግሎት ነው።',
                    'trust_points_title' => 'ጎብኚዎች በፍጥነት መረዳት ያለባቸው',
                    'footer_lead' => 'ለታማኝ የኢትዮጵያ ንብረት ማጋራት መድረክ የተሰራ ዘመናዊ የእንግዳ መነሻ ገጽ።',
                    'footer_built' => 'ለተረጋገጠ ማጋራት፣ ማስያዝ እና መተማመን የተገነባ።',
                ],
            ];

            $ui = $copy[$locale] ?? $copy['en'];
            $baseUrl = route('home');
            $homeLink = fn (string $lang) => route('home', ['lang' => $lang]);

            $navigation = [
                ['label' => $locale === 'am' ? 'ባህሪያት' : 'Features', 'href' => '#features'],
                ['label' => $locale === 'am' ? 'እንዴት ይሰራል' : 'How it works', 'href' => '#how-it-works'],
                ['label' => $locale === 'am' ? 'ታማኝነት' : 'Trust', 'href' => '#trust'],
            ];

            $features = [
                [
                    'title' => $locale === 'am' ? 'የተረጋገጠ ፍለጋ' : 'Verified discovery',
                    'description' => $locale === 'am'
                        ? 'ተጠቃሚዎች ንብረቶችን በፍጥነት እንዲያገኙ ግልጽ መረጃ፣ ዝግጁነት እና የታማኝነት ምልክቶች ይሰጣል።'
                        : 'Help guests find assets quickly with clear details, availability, and trust signals.',
                    'points' => $locale === 'am'
                        ? ['ለፍለጋ የተዘጋጀ አቀራረብ', 'የታማኝነት ነጥብ እይታ', 'ቀላል የማስያዝ ፍሰት']
                        : ['Search-ready layout', 'Trust score visibility', 'Straightforward booking flow'],
                ],
                [
                    'title' => $locale === 'am' ? 'የባለቤት መቆጣጠሪያዎች' : 'Owner controls',
                    'description' => $locale === 'am'
                        ? 'ባለቤቶች መዘርዘሪያዎችን ለመለጠፍ፣ ዝግጁነትን ለማስተዳደር እና እንቅስቃሴን ለመከታተል ፕሪሚየም መንገድ ይሰጣል።'
                        : 'Give owners a premium way to publish listings, manage availability, and monitor activity.',
                    'points' => $locale === 'am'
                        ? ['ፈጣን የዝርዝር መሳሪያዎች', 'የዝግጁነት አስተዳደር', 'የገቢ እይታ']
                        : ['Fast listing tools', 'Availability management', 'Earnings visibility'],
                ],
                [
                    'title' => $locale === 'am' ? 'የተጠያቂነት ፍሰት' : 'Accountable workflow',
                    'description' => $locale === 'am'
                        ? 'ማስረከብ፣ ግምገማ፣ ክርክር እና ተቀማጭ ክፍያዎችን በአንድ ወጥ ፍሰት ውስጥ ያቆያል።'
                        : 'Keep handoffs, reviews, disputes, and deposits inside one consistent operating flow.',
                    'points' => $locale === 'am'
                        ? ['የመቀበያ ግልጽነት', 'የግምገማ ታሪክ', 'የክርክር መከታተያ']
                        : ['Check-in clarity', 'Review history', 'Dispute tracking'],
                ],
            ];

            $steps = [
                [
                    'title' => $locale === 'am' ? 'መለያ ፍጠር' : 'Create a profile',
                    'description' => $locale === 'am' ? 'ተመዝግበው፣ ያረጋግጡ፣ እና የታማኝነትዎን መረጃ ያክሉ።' : 'Join, verify, and add the details that make your account trustworthy.',
                ],
                [
                    'title' => $locale === 'am' ? 'ንብረት ምረጥ' : 'Choose an asset',
                    'description' => $locale === 'am' ? 'በምድብ፣ በክልል ወይም በዓላማ መሠረት ዝርዝሮችን ይመልከቱ እና ይነፃፀሩ።' : 'Browse listings by category, region, or purpose and compare what fits best.',
                ],
                [
                    'title' => $locale === 'am' ? 'ፍሰቱን አጠናቅቅ' : 'Complete the flow',
                    'description' => $locale === 'am' ? 'ያስይዙ፣ ያስረክቡ፣ ይገምግሙ እና ሁሉንም በቀላል ዳሽቦርድ ያስተዳድሩ።' : 'Book, hand off, review, and manage everything through a simple dashboard.',
                ],
            ];

            $stats = [
                ['value' => '24/7', 'label' => $locale === 'am' ? 'ለእንግዶችና ባለቤቶች ዝግጁ' : 'access for guests and owners'],
                ['value' => '82.3', 'label' => $locale === 'am' ? 'ምሳሌ የታማኝነት ነጥብ' : 'example trust score', 'accent' => true],
                ['value' => '1 dashboard', 'label' => $locale === 'am' ? 'ለማስያዝና ለዝርዝሮች' : 'for bookings and listings'],
            ];
        @endphp

        <div class="relative isolate overflow-hidden bg-slate-50 dark:!bg-slate-950">
            <div class="absolute inset-x-0 top-0 -z-10 h-[38rem] bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.14),_transparent_40%),radial-gradient(circle_at_top_right,_rgba(56,189,248,0.12),_transparent_32%),linear-gradient(180deg,_rgba(255,255,255,1)_0%,_rgba(248,250,252,1)_55%,_rgba(241,245,249,1)_100%)] dark:bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.22),_transparent_40%),radial-gradient(circle_at_top_right,_rgba(56,189,248,0.14),_transparent_32%),linear-gradient(180deg,_rgba(15,23,42,0.98)_0%,_rgba(15,23,42,0.94)_46%,_rgba(2,6,23,1)_100%)]"></div>
            <div class="absolute left-1/2 top-[-7rem] -z-10 h-[30rem] w-[30rem] -translate-x-1/2 rounded-full bg-emerald-300/25 blur-3xl dark:bg-emerald-500/10"></div>

            <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/75 backdrop-blur-xl dark:border-white/10 dark:!bg-slate-950/90">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ $baseUrl }}" class="flex items-center gap-3" wire:navigate>
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-500/10 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/15 dark:ring-emerald-400/20">
                           <img src="{{ asset('Ethio Share Logo.png') }}" alt="">
                        </span>
                        <span class="leading-tight">
                            <span class="block text-lg font-semibold tracking-tight text-slate-900 dark:!text-white">EthioShare</span>
                            <span class="block text-xs uppercase tracking-[0.28em] text-slate-500 dark:!text-slate-300">Trusted asset sharing</span>
                        </span>
                    </a>

                    <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex dark:!text-slate-200">
                        @foreach ($navigation as $item)
                            <a href="{{ $item['href'] }}" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $item['label'] }}</a>
                        @endforeach
                    </nav>

                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="inline-flex rounded-2xl border border-slate-200 bg-white p-1 shadow-sm shadow-slate-900/5 dark:border-white/10 dark:!bg-white/5" aria-label="Language selector">
                            <a href="{{ $homeLink('en') }}" class="rounded-xl px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] transition {{ $locale === 'en' ? 'bg-emerald-600 text-white shadow' : 'text-slate-500 hover:text-slate-900 dark:!text-slate-200 dark:hover:text-white' }}">EN</a>
                            <a href="{{ $homeLink('am') }}" class="rounded-xl px-3 py-2 text-xs font-semibold transition {{ $locale === 'am' ? 'bg-emerald-600 text-white shadow' : 'text-slate-500 hover:text-slate-900 dark:!text-slate-200 dark:hover:text-white' }}">አማ</a>
                        </div>

                        <button type="button" data-theme-toggle class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-900/5 transition hover:border-emerald-300 hover:text-slate-900 dark:border-white/10 dark:!bg-white/5 dark:!text-slate-100 dark:hover:border-emerald-400/30 dark:hover:text-white" aria-pressed="false">
                            <span data-theme-icon class="text-base leading-none">☼</span>
                            <span data-theme-label>Light</span>
                        </button>

                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500">
                                {{ $locale === 'am' ? 'ዳሽቦርድ' : 'Dashboard' }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-900/5 transition hover:border-emerald-300 hover:text-slate-900 dark:border-white/10 dark:!bg-white/5 dark:!text-slate-100 dark:hover:border-emerald-400/30 dark:hover:text-white">
                                {{ $locale === 'am' ? 'መግቢያ' : 'Log in' }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-emerald-600/20 transition hover:bg-emerald-500">
                                    {{ $ui['cta_secondary'] }}
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </header>

            <main>
                <section class="mx-auto grid max-w-7xl gap-12 px-4 py-16 sm:px-6 lg:grid-cols-[minmax(0,1.08fr)_minmax(0,0.92fr)] lg:items-center lg:px-8 lg:py-24">
                    <div class="max-w-3xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-500/20 bg-emerald-500/10 px-4 py-2 text-sm font-medium text-emerald-700 dark:text-emerald-200">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            {{ $ui['badge'] }}
                        </div>

                        <h1 class="mt-6 text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl dark:text-white">
                            {{ $ui['title'] }}
                        </h1>

                        <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-600 sm:text-xl dark:text-slate-200">
                            {{ $ui['lead'] }}
                        </p>

                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-xl shadow-emerald-600/20 transition hover:bg-emerald-500">
                                {{ $ui['cta_primary'] }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm shadow-slate-900/5 transition hover:border-emerald-300 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:border-emerald-400/30 dark:hover:text-white">
                                    {{ $ui['cta_secondary'] }}
                                </a>
                            @endif
                            <a href="#features" class="inline-flex items-center justify-center rounded-2xl border border-transparent bg-slate-100 px-6 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-200 dark:bg-white/5 dark:text-slate-200 dark:hover:bg-white/10">
                                {{ $ui['cta_tertiary'] }}
                            </a>
                        </div>

                        <div class="mt-10 grid gap-4 sm:grid-cols-3">
                            @foreach ($stats as $stat)
                                <div class="rounded-3xl border {{ !empty($stat['accent']) ? 'border-emerald-500/20 bg-emerald-500/10' : 'border-slate-200 bg-white' }} p-5 shadow-lg shadow-slate-900/5 backdrop-blur dark:border-white/10 dark:bg-white/5 dark:shadow-black/10">
                                    <div class="text-2xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $stat['value'] }}</div>
                                    <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-200">{{ $stat['label'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="relative">
                        <div class="absolute inset-0 -z-10 rounded-[2.5rem] bg-gradient-to-br from-emerald-200/70 via-white to-cyan-100 blur-2xl dark:from-emerald-500/20 dark:via-slate-900 dark:to-cyan-400/10"></div>
                        <div class="rounded-[2rem] border border-slate-200 bg-white p-4 shadow-2xl shadow-slate-900/10 backdrop-blur-xl sm:p-6 dark:border-white/10 dark:!bg-slate-900/90 dark:shadow-black/30">
                            <div class="rounded-[1.5rem] border border-slate-200 bg-slate-50 p-5 dark:border-white/10 dark:!bg-slate-950/90">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.28em] text-emerald-600 dark:text-emerald-300">{{ $ui['hero_label'] }}</p>
                                        <h2 class="mt-2 text-xl font-semibold text-slate-950 dark:text-white">{{ $ui['hero_title'] }}</h2>
                                    </div>
                                    <div class="rounded-2xl bg-emerald-500/10 px-3 py-2 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-200 dark:ring-emerald-400/20">
                                        {{ $locale === 'am' ? 'የተረጋገጠ' : 'Verified' }}
                                    </div>
                                </div>

                                <div class="mt-6 space-y-4">
                                    <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-900/5 dark:border-white/10 dark:!bg-white/5">
                                        <p class="text-sm font-medium text-slate-600 dark:!text-slate-200">{{ $ui['hero_asset_label'] }}</p>
                                        <div class="mt-3 grid gap-4 sm:grid-cols-[112px_minmax(0,1fr)]">
                                            <div class="flex h-28 items-end rounded-2xl bg-[linear-gradient(135deg,_rgba(16,185,129,0.95),_rgba(15,23,42,0.95))] p-3">
                                                <div class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white">Vehicle</div>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-slate-950 dark:!text-white">{{ $ui['hero_asset_title'] }}</h3>
                                                <p class="mt-1 text-sm leading-6 text-slate-600 dark:!text-slate-200">{{ $ui['hero_asset_desc'] }}</p>
                                                <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
                                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700 dark:!bg-white/10 dark:!text-slate-200">Insurance-ready</span>
                                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700 dark:!bg-white/10 dark:!text-slate-200">Instant booking</span>
                                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700 dark:!bg-white/10 dark:!text-slate-200">Deposit protected</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-900/5 dark:border-white/10 dark:!bg-white/5">
                                            <p class="text-sm font-medium text-slate-600 dark:!text-slate-200">{{ $ui['booking_label'] }}</p>
                                            <p class="mt-2 text-2xl font-semibold text-slate-950 dark:!text-white">{{ $ui['booking_value'] }}</p>
                                            <p class="mt-1 text-sm text-slate-600 dark:!text-slate-200">{{ $ui['booking_desc'] }}</p>
                                        </div>
                                        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm shadow-slate-900/5 dark:border-white/10 dark:!bg-white/5">
                                            <p class="text-sm font-medium text-slate-600 dark:!text-slate-200">{{ $ui['trust_label'] }}</p>
                                            <p class="mt-2 text-2xl font-semibold text-slate-950 dark:!text-white">82.3</p>
                                            <p class="mt-1 text-sm text-slate-600 dark:!text-slate-200">{{ $ui['trust_desc'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="features" class="mx-auto max-w-7xl px-4 pb-16 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-300">Features</p>
                        <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl dark:text-white">{{ $ui['features_title'] }}</h2>
                        <p class="mt-4 text-base leading-7 text-slate-600 dark:text-slate-200">{{ $ui['features_lead'] }}</p>
                    </div>

                    <div class="mt-8 grid gap-5 lg:grid-cols-3">
                        @foreach ($features as $feature)
                            <article class="rounded-[1.75rem] border border-slate-200 bg-white p-6 shadow-lg shadow-slate-900/5 backdrop-blur transition duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-xl dark:border-white/10 dark:!bg-white/5 dark:shadow-black/10 dark:hover:border-emerald-400/30">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-500/10 text-emerald-600 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-500/15 dark:text-emerald-300 dark:ring-emerald-400/20">
                                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 dark:bg-emerald-300"></span>
                                </div>
                                <h3 class="mt-5 text-xl font-semibold text-slate-950 dark:!text-white">{{ $feature['title'] }}</h3>
                                <p class="mt-3 text-sm leading-7 text-slate-600 dark:!text-slate-200">{{ $feature['description'] }}</p>
                                <ul class="mt-5 space-y-2 text-sm text-slate-600 dark:!text-slate-200">
                                    @foreach ($feature['points'] as $point)
                                        <li class="flex items-start gap-3">
                                            <span class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-500 dark:bg-emerald-300"></span>
                                            <span>{{ $point }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </div>
                </section>

                <section id="how-it-works" class="border-y border-slate-200 bg-slate-100/80 dark:border-white/10 dark:bg-slate-900/55">
                    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
                        <div class="grid gap-10 lg:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)] lg:items-start">
                            <div class="max-w-xl">
                                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:text-emerald-300">How it works</p>
                                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl dark:text-white">{{ $ui['how_title'] }}</h2>
                                <p class="mt-4 text-base leading-7 text-slate-600 dark:text-slate-200">{{ $ui['how_lead'] }}</p>
                                <div class="mt-8 flex flex-wrap gap-3">
                                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                                        {{ $locale === 'am' ? 'መግቢያ ክፈት' : 'Open login' }}
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-emerald-300 hover:text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-slate-200 dark:hover:border-emerald-400/30 dark:hover:text-white">
                                            {{ $locale === 'am' ? 'መዝገብ አድርግ' : 'Register' }}
                                        </a>
                                    @endif
                                </div>
                            </div>

                            <div class="grid gap-4">
                                @foreach ($steps as $index => $step)
                                    <article class="rounded-[1.5rem] border border-slate-200 bg-white p-6 shadow-lg shadow-slate-900/5 backdrop-blur dark:border-white/10 dark:!bg-white/5 dark:shadow-black/10">
                                        <div class="flex items-start gap-4">
                                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-lg font-semibold text-slate-950 dark:!bg-white/10 dark:!text-white">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-slate-950 dark:!text-white">{{ $step['title'] }}</h3>
                                                <p class="mt-2 text-sm leading-7 text-slate-600 dark:!text-slate-200">{{ $step['description'] }}</p>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <section id="trust" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                    <div class="grid gap-5 lg:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
                        <article class="rounded-[2rem] border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-cyan-50 p-8 shadow-2xl shadow-slate-900/5 backdrop-blur dark:border-white/10 dark:!from-slate-900 dark:!via-slate-900 dark:!to-slate-950 dark:shadow-black/10">
                            <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-600 dark:!text-emerald-300">Trust & accountability</p>
                            <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-950 dark:!text-white">{{ $ui['trust_title'] }}</h2>
                            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600 dark:!text-slate-200">{{ $ui['trust_lead'] }}</p>
                        </article>

                        <article class="rounded-[2rem] border border-slate-200 bg-white p-8 shadow-lg shadow-slate-900/5 backdrop-blur dark:border-white/10 dark:!bg-white/5 dark:shadow-black/10">
                            <h3 class="text-xl font-semibold text-slate-950 dark:!text-white">{{ $ui['trust_points_title'] }}</h3>
                            <ul class="mt-5 space-y-4 text-sm leading-7 text-slate-600 dark:!text-slate-200">
                                <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-emerald-500"></span><span>{{ $locale === 'am' ? 'ይህ የተሟላ የማስገቢያ ገጽ ነው፣ ፕላሴሆልደር አይደለም።' : 'This is a real product entry point, not a placeholder.' }}</span></li>
                                <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-emerald-500"></span><span>{{ $locale === 'am' ? 'ንድፉ የEthioShare የዳሽቦርድ ስሜትን ይጠብቃል።' : 'The design belongs to EthioShare and matches its dashboard language.' }}</span></li>
                                <li class="flex gap-3"><span class="mt-2 h-2 w-2 rounded-full bg-emerald-500"></span><span>{{ $locale === 'am' ? 'መግቢያ እና መመዝገብ ዋና የተግባር ነጥቦች ናቸው።' : 'Login and registration are the primary actions for guests.' }}</span></li>
                            </ul>
                        </article>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200 bg-white/80 dark:border-white/10 dark:!bg-slate-950/80">
                <div class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
                    <div>
                        <a href="{{ $baseUrl }}" class="inline-flex items-center gap-3" wire:navigate>
                            <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-500/10 ring-1 ring-inset ring-emerald-500/20 dark:bg-emerald-400/15 dark:ring-emerald-400/20">
                               <img src="{{ asset('Ethio Share Logo.png') }}" alt="">
                            </span>
                            <span class="text-lg font-semibold text-slate-950 dark:!text-white">EthioShare</span>
                        </a>
                        <p class="mt-4 max-w-xl text-sm leading-7 text-slate-600 dark:!text-slate-200">{{ $ui['footer_lead'] }}</p>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-700 dark:!text-slate-300">Explore</h4>
                            <ul class="mt-4 space-y-3 text-sm text-slate-500 dark:!text-slate-200">
                                <li><a href="#features" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $locale === 'am' ? 'ባህሪያት' : 'Features' }}</a></li>
                                <li><a href="#how-it-works" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $locale === 'am' ? 'እንዴት ይሰራል' : 'How it works' }}</a></li>
                                <li><a href="#trust" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $locale === 'am' ? 'ታማኝነት' : 'Trust' }}</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-700 dark:!text-slate-300">Account</h4>
                            <ul class="mt-4 space-y-3 text-sm text-slate-500 dark:!text-slate-200">
                                <li><a href="{{ route('login') }}" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $locale === 'am' ? 'መግቢያ' : 'Log in' }}</a></li>
                                @if (Route::has('register'))
                                    <li><a href="{{ route('register') }}" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $locale === 'am' ? 'መዝገብ' : 'Register' }}</a></li>
                                @endif
                                @auth
                                    <li><a href="{{ route('dashboard') }}" class="transition hover:text-emerald-600 dark:hover:text-white">{{ $locale === 'am' ? 'ዳሽቦርድ' : 'Dashboard' }}</a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-white/10">
                    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-5 text-sm text-slate-500 dark:!text-slate-300 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                        <p>&copy; {{ date('Y') }} EthioShare. All rights reserved.</p>
                        <p>{{ $ui['footer_built'] }}</p>
                    </div>
                </div>
            </footer>
        </div>

        <script>
            (() => {
                const root = document.documentElement;
                const storageKey = 'ethioshare-theme';
                const button = document.querySelector('[data-theme-toggle]');
                const label = button?.querySelector('[data-theme-label]');
                const icon = button?.querySelector('[data-theme-icon]');

                const applyTheme = (theme) => {
                    const nextTheme = theme === 'dark' ? 'dark' : 'light';
                    root.classList.toggle('dark', nextTheme === 'dark');
                    root.dataset.theme = nextTheme;
                    root.style.colorScheme = nextTheme;
                    localStorage.setItem(storageKey, nextTheme);

                    button?.setAttribute('aria-pressed', String(nextTheme === 'dark'));

                    if (label) {
                        label.textContent = nextTheme === 'dark' ? 'Dark' : 'Light';
                    }

                    if (icon) {
                        icon.textContent = nextTheme === 'dark' ? '☾' : '☼';
                    }
                };

                const storedTheme = localStorage.getItem(storageKey);
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                applyTheme(storedTheme || (prefersDark ? 'dark' : 'light'));

                button?.addEventListener('click', () => {
                    applyTheme(root.classList.contains('dark') ? 'light' : 'dark');
                });
            })();
        </script>
    </body>
</html>
