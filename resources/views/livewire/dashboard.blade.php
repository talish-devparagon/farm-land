<div class="space-y-8">
    <div>
        <flux:heading size="xl">{{ __('Dashboard') }}</flux:heading>
        <flux:text class="mt-1">{{ __("A live snapshot of your herd, health, and breeding activity.") }}</flux:text>
    </div>

    {{-- Herd overview stat cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <flux:card class="relative overflow-hidden p-5">
            <div class="pointer-events-none absolute -top-6 -right-6 size-24 rounded-full bg-cyan-500/10 blur-2xl dark:bg-cyan-400/20"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Total herd') }}</flux:text>
                    <flux:heading size="xl" class="mt-1 font-mono tabular-nums">{{ number_format($this->herdOverview['total']) }}</flux:heading>
                </div>
                <flux:icon.rectangle-stack variant="micro" class="size-5 shrink-0 text-cyan-600 dark:text-cyan-400" />
            </div>
        </flux:card>

        <flux:card class="relative overflow-hidden p-5">
            <div class="pointer-events-none absolute -top-6 -right-6 size-24 rounded-full bg-green-500/10 blur-2xl dark:bg-green-400/20"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Alive') }}</flux:text>
                    <flux:heading size="xl" class="mt-1 font-mono tabular-nums">{{ number_format($this->herdOverview['alive']) }}</flux:heading>
                </div>
                <flux:icon.heart variant="micro" class="size-5 shrink-0 text-green-600 dark:text-green-400" />
            </div>
        </flux:card>

        <flux:card class="relative overflow-hidden p-5">
            <div class="pointer-events-none absolute -top-6 -right-6 size-24 rounded-full bg-blue-500/10 blur-2xl dark:bg-blue-400/20"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Sold') }}</flux:text>
                    <flux:heading size="xl" class="mt-1 font-mono tabular-nums">{{ number_format($this->herdOverview['sold']) }}</flux:heading>
                </div>
                <flux:icon.banknotes variant="micro" class="size-5 shrink-0 text-blue-600 dark:text-blue-400" />
            </div>
        </flux:card>

        <flux:card class="relative overflow-hidden p-5">
            <div class="pointer-events-none absolute -top-6 -right-6 size-24 rounded-full bg-zinc-500/10 blur-2xl dark:bg-zinc-400/10"></div>
            <div class="relative flex items-start justify-between gap-2">
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Deceased') }}</flux:text>
                    <flux:heading size="xl" class="mt-1 font-mono tabular-nums">{{ number_format($this->herdOverview['deceased']) }}</flux:heading>
                </div>
                <flux:icon.archive-box variant="micro" class="size-5 shrink-0 text-zinc-500 dark:text-zinc-400" />
            </div>
        </flux:card>

        <flux:card class="relative col-span-2 overflow-hidden p-5 sm:col-span-1">
            <div class="pointer-events-none absolute -top-6 -right-6 size-24 rounded-full bg-violet-500/10 blur-2xl dark:bg-violet-400/20"></div>
            <div class="relative">
                <div class="flex items-start justify-between gap-2">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Male / Female') }}</flux:text>
                    <flux:icon.users variant="micro" class="size-5 shrink-0 text-violet-600 dark:text-violet-400" />
                </div>
                <flux:heading size="xl" class="mt-1 font-mono tabular-nums">
                    {{ number_format($this->herdOverview['male']) }} <span class="text-zinc-400 dark:text-zinc-500">/</span> {{ number_format($this->herdOverview['female']) }}
                </flux:heading>
                <div class="mt-2 flex h-1.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                    <div class="h-full bg-blue-500" style="width: {{ $this->herdOverview['malePercent'] }}%"></div>
                    <div class="h-full bg-rose-500" style="width: {{ $this->herdOverview['femalePercent'] }}%"></div>
                </div>
            </div>
        </flux:card>
    </div>

    {{-- Chart widgets --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <livewire:dashboard.herd-growth-chart />
        <livewire:dashboard.weight-trend-chart />
        <livewire:dashboard.breed-distribution-chart />
    </div>

    {{-- Reminders and activity --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <livewire:dashboard.upcoming-health-reminders />
        <livewire:dashboard.upcoming-breeding-reminders />
        {{--<livewire:dashboard.recent-activity />--}}
    </div>
</div>
