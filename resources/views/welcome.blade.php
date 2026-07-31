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
                    <a href="#features" class="hover:text-zinc-900 dark:hover:text-white">Features</a>
                    <a href="#pricing" class="hover:text-zinc-900 dark:hover:text-white">Pricing</a>
                    <a href="#about" class="hover:text-zinc-900 dark:hover:text-white">About</a>
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

            <div class="mx-auto max-w-5xl px-6 py-24 text-center sm:py-32">
                <span class="inline-flex items-center gap-2 rounded-full border border-zinc-200 px-4 py-1 text-xs font-medium text-zinc-600 dark:border-zinc-800 dark:text-zinc-400">
                    <flux:icon.sparkles variant="micro" class="size-3.5 text-accent" />
                    Built for cattle & dairy farms
                </span>

                <h1 class="mt-6 text-4xl font-bold tracking-tight text-balance sm:text-6xl">
                    Run your herd like the thriving business it is.
                </h1>

                <p class="mx-auto mt-6 max-w-2xl text-lg text-zinc-600 dark:text-zinc-400">
                    Track every animal, breeding cycle, health record, and weight gain in one place — so you spend less time on paperwork and more time on the farm.
                </p>

                <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="primary" class="h-12 px-6 text-base">Go to dashboard</flux:button>
                    @else
                        <flux:button href="{{ route('register') }}" variant="primary" class="h-12 px-6 text-base">Start free trial</flux:button>
                        <flux:button href="#pricing" variant="ghost" class="h-12 px-6 text-base">See pricing</flux:button>
                    @endauth
                </div>
            </div>
        </section>

        {{-- Feature cards --}}
        <section id="features" class="mx-auto max-w-7xl px-6 py-20">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Everything your herd records need</h2>
                <p class="mt-4 text-zinc-600 dark:text-zinc-400">No spreadsheets, no lost notebooks — just a clear picture of every animal on your farm.</p>
            </div>

            <div class="mt-14 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @php
                    $features = [
                        ['icon' => 'identification', 'title' => 'Animal Records', 'body' => 'Keep a full profile for every animal — breed, status, lineage, and history at a glance.'],
                        ['icon' => 'heart', 'title' => 'Health Tracking', 'body' => 'Log vaccinations, treatments, and vet visits so nothing falls through the cracks.'],
                        ['icon' => 'sparkles', 'title' => 'Breeding Records', 'body' => 'Track breeding cycles and get reminders for upcoming calving dates.'],
                        ['icon' => 'scale', 'title' => 'Weight Logs', 'body' => 'Monitor growth trends over time to catch problems early and plan feed better.'],
                        ['icon' => 'bell-alert', 'title' => 'Upcoming Reminders', 'body' => 'Never miss a due date — health and breeding reminders land right on your dashboard.'],
                        ['icon' => 'chart-bar', 'title' => 'Reports & Insights', 'body' => 'See herd growth, breed distribution, and trends with simple, clear reports.'],
                    ];
                @endphp

                @foreach ($features as $feature)
                    <div class="rounded-xl border border-zinc-200 p-6 transition hover:border-zinc-300 hover:shadow-sm dark:border-zinc-800 dark:hover:border-zinc-700">
                        <div class="flex size-10 items-center justify-center rounded-lg bg-accent-content text-accent-foreground">
                            <flux:icon :name="$feature['icon']" variant="outline" class="size-5" />
                        </div>
                        <h3 class="mt-4 font-semibold">{{ $feature['title'] }}</h3>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $feature['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Promo strip --}}
        <section class="border-y border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900/50">
            <div class="mx-auto grid max-w-5xl grid-cols-2 gap-8 px-6 py-14 text-center sm:grid-cols-4">
                <div>
                    <p class="text-3xl font-bold">2,400+</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Farms onboarded</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">180k+</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Animals tracked</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">99.9%</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Uptime</p>
                </div>
                <div>
                    <p class="text-3xl font-bold">4.9/5</p>
                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Farmer rating</p>
                </div>
            </div>
        </section>

        {{-- Pricing --}}
        <section id="pricing" class="mx-auto max-w-6xl px-6 py-20" x-data="{ yearly: false }">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Simple pricing, no surprises</h2>
                <p class="mt-4 text-zinc-600 dark:text-zinc-400">Start free. Upgrade as your herd grows.</p>
            </div>

            <div class="mt-8 flex items-center justify-center gap-3">
                <span class="text-sm font-medium" :class="!yearly && 'text-zinc-900 dark:text-white'">Monthly</span>
                <button
                    type="button"
                    role="switch"
                    :aria-checked="yearly"
                    @click="yearly = !yearly"
                    class="relative inline-flex h-6 w-11 items-center rounded-full bg-zinc-300 transition dark:bg-zinc-700"
                    :class="yearly && '!bg-accent'"
                >
                    <span class="inline-block size-4 transform rounded-full bg-white transition" :class="yearly ? 'translate-x-6' : 'translate-x-1'"></span>
                </button>
                <span class="text-sm font-medium" :class="yearly && 'text-zinc-900 dark:text-white'">
                    Yearly <span class="text-accent">(save 20%)</span>
                </span>
            </div>

            <div class="mt-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
                @php
                    $plans = [
                        ['name' => 'Starter', 'desc' => 'For small herds getting organized.', 'monthly' => 9, 'yearly' => 7, 'features' => ['Up to 25 animals', 'Health & breeding records', 'Weight logs', 'Email support'], 'featured' => false],
                        ['name' => 'Growth', 'desc' => 'For growing operations.', 'monthly' => 29, 'yearly' => 23, 'features' => ['Up to 250 animals', 'Everything in Starter', 'Reports & insights', 'Priority support'], 'featured' => true],
                        ['name' => 'Enterprise', 'desc' => 'For large farms & co-ops.', 'monthly' => 79, 'yearly' => 63, 'features' => ['Unlimited animals', 'Everything in Growth', 'Multi-farm management', 'Dedicated support'], 'featured' => false],
                    ];
                @endphp

                @foreach ($plans as $plan)
                    <div
                        x-data="{ monthly: {{ $plan['monthly'] }}, yearlyPrice: {{ $plan['yearly'] }} }"
                        class="rounded-2xl border p-8 {{ $plan['featured'] ? 'border-accent shadow-lg' : 'border-zinc-200 dark:border-zinc-800' }}"
                    >
                        @if ($plan['featured'])
                            <flux:badge color="cyan" size="sm">Most popular</flux:badge>
                        @endif

                        <h3 class="mt-3 text-lg font-semibold">{{ $plan['name'] }}</h3>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $plan['desc'] }}</p>

                        <p class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-bold" x-text="'$' + (yearly ? yearlyPrice : monthly)"></span>
                            <span class="text-sm text-zinc-500 dark:text-zinc-400">/mo</span>
                        </p>

                        <flux:button
                            :href="auth()->check() ? route('dashboard') : route('register')"
                            :variant="$plan['featured'] ? 'primary' : 'ghost'"
                            class="mt-6 w-full justify-center"
                        >
                            Get started
                        </flux:button>

                        <ul class="mt-8 space-y-3 text-sm">
                            @foreach ($plan['features'] as $item)
                                <li class="flex items-center gap-2">
                                    <flux:icon.check variant="micro" class="size-4 text-accent" />
                                    {{ $item }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- About --}}
        <section id="about" class="border-t border-zinc-200 dark:border-zinc-800">
            <div class="mx-auto max-w-3xl px-6 py-20 text-center">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Why we built {{ config('app.name', 'this') }}</h2>
                <p class="mt-6 text-zinc-600 dark:text-zinc-400">
                    We grew up around farms where herd records lived in notebooks, spreadsheets, and memory.
                    {{ config('app.name', 'This app') }} exists to give farm owners a simple, reliable way to track
                    every animal's story — so good decisions are backed by good records, not guesswork.
                </p>
            </div>
        </section>

        {{-- CTA --}}
        <section class="mx-auto max-w-5xl px-6 pb-20">
            <div class="rounded-2xl bg-zinc-900 px-8 py-14 text-center dark:bg-accent-content">
                <h2 class="text-2xl font-bold text-white sm:text-3xl dark:text-accent-foreground">Ready to get your herd in order?</h2>
                <p class="mt-3 text-zinc-300 dark:text-accent-foreground/80">Join thousands of farm owners already managing their herd with confidence.</p>
                <div class="mt-8">
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="primary" class="h-12 px-6 text-base">Go to dashboard</flux:button>
                    @else
                        <flux:button href="{{ route('register') }}" variant="primary" class="h-12 px-6 text-base">Start free trial</flux:button>
                    @endauth
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-zinc-200 py-8 dark:border-zinc-800">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-4 px-6 text-sm text-zinc-500 sm:flex-row">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</span>
                <div class="flex items-center gap-6">
                    <a href="#features" class="hover:text-zinc-900 dark:hover:text-white">Features</a>
                    <a href="#pricing" class="hover:text-zinc-900 dark:hover:text-white">Pricing</a>
                    <a href="#about" class="hover:text-zinc-900 dark:hover:text-white">About</a>
                </div>
            </div>
        </footer>

        @fluxScripts
    </body>
</html>
