<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600,700,900,500i,600i|jetbrains-mono:400,500,600&display=swap" rel="stylesheet" />
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        @php
            $display = 'font-[Fraunces]';
            $mono = 'font-[JetBrains_Mono]';
            $tagCut = '[clip-path:polygon(18px_0,100%_0,100%_100%,0_100%,0_18px)] [mask-image:radial-gradient(circle_5px_at_15px_15px,transparent_97%,#000_100%)] [-webkit-mask-image:radial-gradient(circle_5px_at_15px_15px,transparent_97%,#000_100%)]';
            $tagCutSm = '[clip-path:polygon(12px_0,100%_0,100%_100%,0_100%,0_12px)] [mask-image:radial-gradient(circle_3.5px_at_10px_10px,transparent_96%,#000_100%)] [-webkit-mask-image:radial-gradient(circle_3.5px_at_10px_10px,transparent_96%,#000_100%)]';
        @endphp

        {{-- Nav --}}
        <header class="sticky top-0 z-40 border-b border-zinc-200/80 bg-white/80 backdrop-blur dark:border-zinc-800/80 dark:bg-zinc-950/80">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-6 py-4">
                <x-app-logo />

                <nav class="hidden items-center gap-8 text-sm font-medium text-zinc-600 md:flex dark:text-zinc-400">
                    <a href="#features" class="transition hover:text-zinc-900 dark:hover:text-white">Features</a>
                    <a href="#how" class="transition hover:text-zinc-900 dark:hover:text-white">How it works</a>
                    <a href="#about" class="transition hover:text-zinc-900 dark:hover:text-white">About</a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="primary">Dashboard</flux:button>
                    @else
                        <flux:button href="{{ route('login') }}" variant="ghost">Log in</flux:button>
                        <flux:button href="{{ route('register') }}" variant="primary">Get started free</flux:button>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Hero --}}
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-x-0 -top-40 -z-10 h-96 bg-linear-to-b from-accent/10 to-transparent blur-3xl"></div>

            <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-16 px-6 py-24 sm:py-28 lg:grid-cols-[1.1fr_0.9fr] lg:py-32">
                <div>
                    <div class="flex w-fit items-center gap-2.5 rounded-full border border-zinc-200 py-1.5 pr-4 pl-1.5 text-xs font-medium text-zinc-600 dark:border-zinc-800 dark:text-zinc-300">
                        <span class="flex size-6 items-center justify-center rounded-full bg-[#F2A93B]/15 text-[#F2A93B]">
                            <flux:icon.tag variant="micro" class="size-3.5" />
                        </span>
                        For herds of five or five hundred
                    </div>

                    <h1 class="{{ $display }} mt-8 text-5xl leading-[1.05] font-semibold tracking-tight text-balance sm:text-6xl">
                        The herd book that <em class="text-accent not-italic italic">never</em> gets left in the barn.
                    </h1>

                    <p class="mt-6 max-w-xl text-lg text-zinc-600 dark:text-zinc-300">
                        FarmLand replaces the notebook on the truck dash and the spreadsheet nobody updates — one record per animal, updated the moment something happens, not the next time you remember.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Go to dashboard</flux:button>
                        @else
                            <flux:button href="{{ route('register') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Start free</flux:button>
                            <flux:button href="#how" variant="ghost" class="h-12 px-8 text-base">See how it works</flux:button>
                        @endauth
                    </div>
                </div>

                {{-- Signature: the animal record card, shaped like a punched ear tag --}}
                <div class="relative mx-auto w-full max-w-sm lg:mx-0 lg:justify-self-end">
                    <div class="absolute -top-3 -right-3 -z-10 h-full w-full rounded-2xl border border-[#F2A93B]/30 bg-[#F2A93B]/5"></div>

                    <div class="{{ $tagCut }} relative rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl shadow-zinc-900/5 dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-black/30">
                        <div class="flex items-center justify-between">
                            <div class="{{ $mono }} flex items-center gap-2 text-[11px] font-medium tracking-widest text-zinc-500 uppercase dark:text-zinc-400">
                                <flux:icon.tag variant="micro" class="size-3.5 text-[#F2A93B]" />
                                Animal record
                            </div>
                            <span class="{{ $mono }} flex items-center gap-1.5 text-[10px] text-emerald-600 dark:text-emerald-400">
                                <span class="size-1.5 animate-pulse rounded-full bg-emerald-500"></span>
                                Live
                            </span>
                        </div>

                        <div class="mt-5 flex items-baseline gap-3 border-t border-dashed border-zinc-200 pt-5 dark:border-zinc-700">
                            <span class="{{ $mono }} text-3xl font-semibold">#0714</span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">Angus Cross · Cow</span>
                        </div>
                        <span class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span> Active
                        </span>

                        <div class="mt-6 space-y-4 border-t border-zinc-200 pt-5 dark:border-zinc-700">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Weight</span>
                                <svg viewBox="0 0 100 24" class="h-6 w-20 text-accent" aria-hidden="true">
                                    <polyline points="0,20 15,17 30,18 45,12 60,13 75,6 100,4" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="{{ $mono }} text-xs font-medium">642 kg</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-zinc-500 dark:text-zinc-400">Health</span>
                                <span class="text-zinc-700 dark:text-zinc-300">✓ BVD vaccine · Feb 12</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-zinc-500 dark:text-zinc-400">Breeding</span>
                                <span class="text-zinc-700 dark:text-zinc-300">Due Apr 3 · day 264</span>
                            </div>
                        </div>

                        <div class="{{ $mono }} mt-6 border-t border-zinc-200 pt-4 text-[10px] text-zinc-400 dark:border-zinc-700 dark:text-zinc-500">
                            Synced 2 minutes ago
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Feature cards --}}
        <section id="features" class="mx-auto max-w-7xl px-6 py-24">
            <div class="mx-auto max-w-2xl text-center">
                <span class="{{ $mono }} text-xs font-medium tracking-widest text-[#F2A93B] uppercase">Records</span>
                <h2 class="{{ $display }} mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">Every record your herd already keeps</h2>
                <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Health, breeding, weight, and lineage — FarmLand just gives them somewhere permanent to live.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $features = [
                        ['icon' => 'identification', 'code' => 'REC.ID', 'title' => 'Animal Records', 'body' => 'Every animal gets a full card: breed, sex, lineage, and status — the whole story, not just a name.'],
                        ['icon' => 'heart', 'code' => 'REC.HLT', 'title' => 'Health Tracking', 'body' => 'Log vaccinations, treatments, and vet visits the day they happen, while you\'re still standing in the pen.'],
                        ['icon' => 'calendar-days', 'code' => 'REC.BRD', 'title' => 'Breeding Records', 'body' => 'Track cycles and due dates, and get a calving reminder before the barn does.'],
                        ['icon' => 'scale', 'code' => 'REC.WGT', 'title' => 'Weight Logs', 'body' => 'Chart growth over time so you catch a problem early and plan feed with real numbers.'],
                        ['icon' => 'bell-alert', 'code' => 'REC.RMD', 'title' => 'Smart Reminders', 'body' => 'Health checks and breeding dates land on your dashboard the morning they\'re due — not the morning after.'],
                        ['icon' => 'chart-bar', 'code' => 'REC.RPT', 'title' => 'Real Reports', 'body' => 'See herd composition and growth trends at a glance, built straight from your own records.'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="group relative rounded-lg border border-zinc-200 bg-white p-7 transition duration-300 hover:-translate-y-0.5 hover:border-[#F2A93B]/50 hover:shadow-lg hover:shadow-[#F2A93B]/5 dark:border-zinc-800 dark:bg-zinc-900/40">
                        <div class="flex items-center justify-between">
                            <div class="flex size-11 items-center justify-center rounded-md bg-accent/10 text-accent transition duration-300 group-hover:bg-accent/20">
                                <flux:icon :name="$feature['icon']" variant="outline" class="size-5" />
                            </div>
                            <span class="{{ $mono }} text-[10px] font-medium tracking-widest text-[#F2A93B] uppercase">{{ $feature['code'] }}</span>
                        </div>
                        <h3 class="mt-5 text-lg font-semibold">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $feature['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- How It Works --}}
        <section id="how" class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-6 py-24">
                <div class="mx-auto max-w-2xl text-center">
                    <span class="{{ $mono }} text-xs font-medium tracking-widest text-[#F2A93B] uppercase">Getting started</span>
                    <h2 class="{{ $display }} mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">From chaos to clarity in three steps</h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Get up and running in minutes, not hours.</p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <div class="relative flex flex-col items-center text-center">
                        <div class="{{ $tagCutSm }} {{ $mono }} flex h-16 w-16 items-center justify-center rounded-lg border border-[#F2A93B]/40 bg-[#F2A93B]/10 text-xl font-semibold text-[#F2A93B]">
                            1
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Create your farm</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Sign up and add your farm details. Sixty seconds, no walkthrough required.</p>
                        <div class="absolute top-8 left-[60%] hidden h-px w-[calc(100%-6rem)] border-t-2 border-dashed border-[#F2A93B]/30 sm:block"></div>
                    </div>

                    <div class="relative flex flex-col items-center text-center">
                        <div class="{{ $tagCutSm }} {{ $mono }} flex h-16 w-16 items-center justify-center rounded-lg border border-[#F2A93B]/40 bg-[#F2A93B]/10 text-xl font-semibold text-[#F2A93B]">
                            2
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Add your animals</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Bring in your herd with breed, sex, status, and lineage — at your own pace, one tag at a time.</p>
                        <div class="absolute top-8 left-[60%] hidden h-px w-[calc(100%-6rem)] border-t-2 border-dashed border-[#F2A93B]/30 sm:block"></div>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <div class="{{ $tagCutSm }} {{ $mono }} flex h-16 w-16 items-center justify-center rounded-lg border border-[#F2A93B]/40 bg-[#F2A93B]/10 text-xl font-semibold text-[#F2A93B]">
                            3
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Start tracking</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Log visits, weigh-ins, and breeding dates. Reports build themselves from there.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Free Forever --}}
        <section class="mx-auto max-w-5xl px-6 py-24">
            <div class="rounded-2xl border border-zinc-200 bg-zinc-50 p-8 dark:border-zinc-800 dark:bg-zinc-900/40 sm:p-14">
                <div class="mx-auto max-w-2xl text-center">
                    <span class="{{ $mono }} text-xs font-medium tracking-widest text-[#F2A93B] uppercase">Pricing</span>
                    <h2 class="{{ $display }} mt-3 text-4xl font-semibold tracking-tight sm:text-5xl">Free. Forever. <em class="text-accent not-italic italic">No fine print.</em></h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                        No credit card, no seat limits, no "talk to sales" for tools you need every day. FarmLand is free because good records shouldn't be the expensive part of farming.
                    </p>

                    <div class="mt-10">
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary" class="px-8">Go to dashboard</flux:button>
                        @else
                            <flux:button href="{{ route('register') }}" variant="primary" class="px-8">Start using FarmLand</flux:button>
                        @endauth
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-4 border-t border-zinc-200 pt-8 text-sm text-zinc-600 sm:grid-cols-3 dark:border-zinc-800 dark:text-zinc-400">
                        <div class="flex items-center justify-center gap-2">
                            <flux:icon.check class="size-4 text-[#F2A93B]" />
                            <span>Every feature, day one</span>
                        </div>
                        <div class="flex items-center justify-center gap-2">
                            <flux:icon.check class="size-4 text-[#F2A93B]" />
                            <span>Unlimited animals & records</span>
                        </div>
                        <div class="flex items-center justify-center gap-2">
                            <flux:icon.check class="size-4 text-[#F2A93B]" />
                            <span>Free now, free for good</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- About --}}
        <section id="about" class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-3xl px-6 py-24 text-center">
                <span class="{{ $mono }} text-xs font-medium tracking-widest text-[#F2A93B] uppercase">Why FarmLand</span>
                <p class="{{ $display }} mt-6 text-3xl leading-snug font-medium text-balance italic sm:text-4xl">
                    "We grew up around farms where the herd book lived in a truck dash, a kitchen drawer, and nowhere, all at once."
                </p>
                <p class="mx-auto mt-6 max-w-xl text-lg text-zinc-600 dark:text-zinc-400">
                    FarmLand exists so every animal's story — health, lineage, weight, breeding — lives in one place your whole operation can trust. Not fancier records. Just records that are actually there when you need them.
                </p>
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="mx-auto max-w-5xl px-6 pb-24">
            <div class="relative overflow-hidden rounded-2xl bg-zinc-900 px-8 py-16 text-center dark:bg-zinc-900">
                <div class="pointer-events-none absolute inset-x-0 -top-24 -z-10 h-64 bg-linear-to-b from-accent/10 to-transparent blur-3xl"></div>

                <h2 class="{{ $display }} text-3xl font-semibold text-white sm:text-4xl">
                    Your herd already keeps records.<br class="hidden sm:block"> Give them somewhere to live.
                </h2>
                <p class="mx-auto mt-3 max-w-xl text-zinc-400">Join the farm owners who closed the notebook for good.</p>

                <div class="mt-10">
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Go to dashboard</flux:button>
                    @else
                        <flux:button href="{{ route('register') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Get started free</flux:button>
                    @endauth
                </div>

                <p class="{{ $mono }} mt-6 text-xs tracking-widest text-zinc-500 uppercase">Free forever &middot; No credit card &middot; Two minute setup</p>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 px-6 py-8 sm:flex-row">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">&copy; {{ date('Y') }} {{ config('app.name', 'FarmLand') }}. All rights reserved.</span>
                </div>
                <nav class="flex items-center gap-8 text-sm text-zinc-600 dark:text-zinc-400">
                    <a href="#features" class="transition hover:text-zinc-900 dark:hover:text-white">Features</a>
                    <a href="#how" class="transition hover:text-zinc-900 dark:hover:text-white">How it works</a>
                    <a href="#about" class="transition hover:text-zinc-900 dark:hover:text-white">About</a>
                </nav>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
