<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">

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
            <div class="pointer-events-none absolute inset-x-0 -top-40 -z-10 h-96 bg-linear-to-b from-accent/20 to-transparent blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 right-0 -z-10 h-80 w-80 bg-linear-to-tl from-accent/10 to-transparent blur-3xl"></div>

            <div class="mx-auto max-w-5xl px-6 py-32 text-center sm:py-48">
                <span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-4 py-1.5 text-xs font-medium text-zinc-600 dark:border-zinc-800 dark:text-zinc-300">
                    <flux:icon.sparkles variant="micro" class="size-3.5 text-accent" />
                    Designed for cattle & dairy farmers
                </span>

                <h1 class="mt-8 text-5xl font-bold tracking-tight text-balance sm:text-7xl leading-tight">
                    Stop losing records.<br>Start winning with data.
                </h1>

                <p class="mx-auto mt-8 max-w-2xl text-lg text-zinc-600 dark:text-zinc-300">
                    FarmLand transforms scattered notebooks and spreadsheets into a single, powerful record keeper. Track animals, health, breeding, and growth — so every decision is backed by real data.
                </p>

                <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="primary" class="h-12 px-8 text-base">Go to dashboard</flux:button>
                    @else
                        <flux:button href="{{ route('register') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Start free now</flux:button>
                        <flux:button href="#how" variant="ghost" class="h-12 px-8 text-base">See how it works</flux:button>
                    @endauth
                </div>
            </div>
        </section>

        {{-- Feature cards --}}
        <section id="features" class="mx-auto max-w-7xl px-6 py-24">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">Built for the work you actually do</h2>
                <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Every feature designed around real farm workflows — nothing fancy, nothing useless.</p>
            </div>

            <div class="mt-16 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $features = [
                        ['icon' => 'identification', 'title' => 'Animal Records', 'body' => 'Complete profile for every animal: breed, status, lineage, sex, and full history. No more guessing which cow is which.'],
                        ['icon' => 'heart', 'title' => 'Health Tracking', 'body' => 'Log every vaccination, treatment, and vet visit in one place. Catch health issues before they spread.'],
                        ['icon' => 'sparkles', 'title' => 'Breeding Records', 'body' => 'Track breeding cycles, due dates, and calving reminders. Stay ahead of your cycle calendar.'],
                        ['icon' => 'scale', 'title' => 'Weight Logs', 'body' => 'Monitor growth over time with visual trends. Spot problems early and optimize feed planning.'],
                        ['icon' => 'bell-alert', 'title' => 'Smart Reminders', 'body' => 'Never miss a health check or breeding date. Reminders land on your dashboard exactly when you need them.'],
                        ['icon' => 'chart-bar', 'title' => 'Real Reports', 'body' => 'See herd composition, breed distribution, and growth trends at a glance. Make decisions backed by data.'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="group relative rounded-2xl border border-zinc-200 bg-white p-8 transition duration-300 hover:border-accent/50 hover:shadow-lg hover:shadow-accent/5 dark:border-zinc-800 dark:bg-zinc-900/50 dark:hover:border-accent/40">
                        <div class="flex size-12 items-center justify-center rounded-xl bg-accent/10 text-accent transition duration-300 group-hover:bg-accent/20">
                            <flux:icon :name="$feature['icon']" variant="outline" class="size-6" />
                        </div>
                        <h3 class="mt-6 text-lg font-bold">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">{{ $feature['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- How It Works --}}
        <section id="how" class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto max-w-7xl px-6 py-24">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">From chaos to clarity in three steps</h2>
                    <p class="mt-4 text-lg text-zinc-600 dark:text-zinc-400">Get up and running in minutes, not hours.</p>
                </div>

                <div class="mt-16 grid grid-cols-1 gap-8 sm:grid-cols-3">
                    <div class="relative flex flex-col items-center text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-accent/10 text-2xl font-bold text-accent ring-4 ring-accent/20">
                            1
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Create your farm</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Sign up and add your farm details. Takes 60 seconds.</p>
                        <div class="absolute top-6 left-[60%] hidden h-1 w-[calc(100%-6rem)] bg-gradient-to-r from-accent/30 to-transparent sm:block"></div>
                    </div>

                    <div class="relative flex flex-col items-center text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-accent/10 text-2xl font-bold text-accent ring-4 ring-accent/20">
                            2
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Add your animals</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Input your herd with breed, sex, status, and lineage. Go at your own pace.</p>
                        <div class="absolute top-6 left-[60%] hidden h-1 w-[calc(100%-6rem)] bg-gradient-to-r from-accent/30 to-transparent sm:block"></div>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-accent/10 text-2xl font-bold text-accent ring-4 ring-accent/20">
                            3
                        </div>
                        <h3 class="mt-6 text-xl font-bold">Start tracking</h3>
                        <p class="mt-2 text-zinc-600 dark:text-zinc-400">Log health visits, breeding dates, and weights. Watch reports build automatically.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Free Forever --}}
        <section class="mx-auto max-w-5xl px-6 py-24">
            <div class="relative rounded-3xl border border-accent/30 bg-gradient-to-br from-accent/5 to-transparent p-8 sm:p-12">
                <div class="pointer-events-none absolute inset-0 rounded-3xl bg-linear-to-br from-accent/5 via-transparent to-accent/5 blur-2xl"></div>

                <div class="relative text-center">
                    <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">Free. Forever. For everyone.</h2>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                        No credit card needed. No surprise charges. No hidden enterprise plans. FarmLand is free because great farm records shouldn't cost money — they should save it.
                    </p>

                    <div class="mt-10">
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary" size="lg" class="px-8">Go to dashboard</flux:button>
                        @else
                            <flux:button href="{{ route('register') }}" variant="primary" size="lg" class="px-8">Start using FarmLand</flux:button>
                        @endauth
                    </div>

                    <div class="mt-8 flex flex-col items-center justify-center gap-3 text-sm text-zinc-600 dark:text-zinc-400">
                        <div class="flex items-center gap-2">
                            <flux:icon.check class="size-5 text-accent" />
                            <span>All features included</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:icon.check class="size-5 text-accent" />
                            <span>Unlimited animals & records</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <flux:icon.check class="size-5 text-accent" />
                            <span>Forever free, always yours</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- About --}}
        <section id="about" class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-3xl px-6 py-24 text-center">
                <h2 class="text-4xl font-bold tracking-tight sm:text-5xl">Why FarmLand exists</h2>
                <p class="mt-6 text-lg text-zinc-600 dark:text-zinc-400">
                    We built FarmLand because we watched farm owners lose time to paperwork and lose animals to forgotten records.
                    The best farms run on data, not guesswork — and the best farmers deserve tools that work as hard as they do.
                    FarmLand is here so every farm owner, from small homesteads to large operations, can manage their herd with confidence.
                </p>
            </div>
        </section>

        {{-- Final CTA --}}
        <section class="mx-auto max-w-5xl px-6 pb-24">
            <div class="relative overflow-hidden rounded-3xl border border-accent/30 bg-gradient-to-r from-zinc-900 via-zinc-900/95 to-accent/10 px-8 py-16 text-center dark:from-zinc-800 dark:via-zinc-800/95 dark:to-accent/10">
                <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-accent/20 via-transparent to-transparent blur-3xl"></div>

                <div class="relative">
                    <h2 class="text-3xl font-bold text-white sm:text-4xl">Your farm deserves better records</h2>
                    <p class="mx-auto mt-3 max-w-xl text-zinc-300">Join farm owners who've ditched the spreadsheets and made smarter decisions.</p>

                    <div class="mt-10">
                        @auth
                            <flux:button href="{{ route('dashboard') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Go to dashboard</flux:button>
                        @else
                            <flux:button href="{{ route('register') }}" variant="primary" class="h-12 px-8 text-base font-semibold">Get started free</flux:button>
                        @endauth
                    </div>
                </div>
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
