<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Reports & Analytics') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Herd, health, breeding, and growth trends for your farm.') }}</flux:text>
        </div>

        <flux:radio.group wire:model.live="rangeMonths" variant="segmented" size="sm">
            @foreach ($this->availableRanges as $range)
                <flux:radio wire:key="range-{{ $range }}" value="{{ $range }}" label="{{ __(':n mo', ['n' => $range]) }}" />
            @endforeach
        </flux:radio.group>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Herd composition --}}
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Herd composition') }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Current herd snapshot &middot; not affected by the date range') }}</flux:text>
                </div>
                <flux:icon.chart-pie variant="micro" class="size-4 shrink-0 text-indigo-600 dark:text-indigo-400" />
            </div>

            @if ($this->herdComposition['total'] === 0)
                <div class="flex h-40 flex-col items-center justify-center gap-2">
                    <flux:icon.rectangle-stack variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('No animals recorded yet.') }}</flux:text>
                </div>
            @else
                <div>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Total animals') }}</flux:text>
                    <flux:heading size="xl" class="font-mono tabular-nums">{{ number_format($this->herdComposition['total']) }}</flux:heading>
                </div>

                {{-- Breed breakdown --}}
                <ul class="space-y-2.5">
                    @foreach ($this->herdBreedBars as $bar)
                        <li wire:key="comp-breed-{{ $bar['label'] }}">
                            <div class="flex items-center justify-between gap-2 text-sm">
                                <span class="truncate text-zinc-700 dark:text-zinc-300">{{ $bar['label'] }}</span>
                                <span class="shrink-0 font-mono text-zinc-500 tabular-nums dark:text-zinc-400">{{ $bar['count'] }}</span>
                            </div>
                            <div class="mt-1 h-1.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                                <div class="h-full rounded-full bg-indigo-500 dark:bg-indigo-400" style="width: {{ $bar['percent'] }}%"></div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <flux:separator />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    {{-- Status --}}
                    <div class="space-y-2">
                        <flux:text size="sm" class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Status') }}</flux:text>
                        <ul class="space-y-1.5">
                            @foreach ($this->herdComposition['byStatus'] as $status => $count)
                                <li wire:key="comp-status-{{ $status }}" class="flex items-center justify-between gap-2 text-sm">
                                    <x-animal-status-badge :status="$status" />
                                    <span class="font-mono text-zinc-500 tabular-nums dark:text-zinc-400">{{ $count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Sex --}}
                    <div class="space-y-2">
                        <flux:text size="sm" class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Sex') }}</flux:text>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-blue-600 dark:text-blue-400">{{ __(':count male', ['count' => $this->herdSexSplit['male']]) }}</span>
                            <span class="text-rose-600 dark:text-rose-400">{{ __(':count female', ['count' => $this->herdSexSplit['female']]) }}</span>
                        </div>
                        <div class="flex h-1.5 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                            <div class="h-full bg-blue-500" style="width: {{ $this->herdSexSplit['malePercent'] }}%"></div>
                            <div class="h-full bg-rose-500" style="width: {{ $this->herdSexSplit['femalePercent'] }}%"></div>
                        </div>
                    </div>

                    {{-- Age bracket --}}
                    <div class="space-y-2">
                        <flux:text size="sm" class="font-medium text-zinc-500 dark:text-zinc-400">{{ __('Age') }}</flux:text>
                        <ul class="space-y-1.5">
                            @foreach ($this->herdAgeBars as $bar)
                                <li wire:key="comp-age-{{ $bar['label'] }}" class="text-sm">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="truncate text-zinc-700 dark:text-zinc-300">{{ $bar['label'] }}</span>
                                        <span class="font-mono text-zinc-500 tabular-nums dark:text-zinc-400">{{ $bar['count'] }}</span>
                                    </div>
                                    <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-white/10">
                                        <div class="h-full rounded-full bg-teal-500 dark:bg-teal-400" style="width: {{ $bar['percent'] }}%"></div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </flux:card>

        {{-- Health summary --}}
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Health summary') }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Records over the last :n months', ['n' => $rangeMonths]) }}</flux:text>
                </div>
                <flux:icon.shield-check variant="micro" class="size-4 shrink-0 text-emerald-600 dark:text-emerald-400" />
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-lg bg-zinc-100/70 p-3 dark:bg-white/5">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Total') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums">{{ number_format($this->healthSummary['totalRecords']) }}</flux:heading>
                </div>
                <div class="rounded-lg bg-rose-50 p-3 dark:bg-rose-500/10">
                    <flux:text size="sm" class="text-rose-600 dark:text-rose-400">{{ __('Overdue') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums text-rose-700 dark:text-rose-300">{{ number_format($this->healthSummary['overdueCount']) }}</flux:heading>
                </div>
                <div class="rounded-lg bg-amber-50 p-3 dark:bg-amber-500/10">
                    <flux:text size="sm" class="text-amber-600 dark:text-amber-400">{{ __('Upcoming') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums text-amber-700 dark:text-amber-300">{{ number_format($this->healthSummary['upcomingCount']) }}</flux:heading>
                </div>
            </div>

            <ul class="space-y-2">
                @foreach ($this->healthSummary['byType'] as $type => $count)
                    <li wire:key="health-type-{{ $type }}" class="flex items-center justify-between gap-2 text-sm">
                        <x-health-record-type-badge :type="$type" />
                        <span class="font-mono text-zinc-500 tabular-nums dark:text-zinc-400">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>

            <flux:separator />

            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Monthly trend') }}</flux:text>
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="mt-2 h-20 w-full overflow-visible" role="img" aria-label="{{ __('Bar chart of health records per month') }}">
                    <line x1="0" y1="98" x2="100" y2="98" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="0.5" />
                    @foreach ($this->healthTrendBars as $bar)
                        <rect
                            wire:key="health-trend-{{ $loop->index }}"
                            x="{{ $bar['x'] }}"
                            y="{{ $bar['y'] }}"
                            width="{{ $bar['width'] }}"
                            height="{{ $bar['height'] }}"
                            rx="1"
                            class="fill-emerald-500 dark:fill-emerald-400"
                        >
                            <title>{{ $bar['label'] }}: {{ $bar['value'] }}</title>
                        </rect>
                    @endforeach
                </svg>
                <div class="mt-1 flex flex-wrap justify-between gap-x-2 text-[10px] text-zinc-500 dark:text-zinc-400">
                    @foreach ($this->healthTrendBars as $bar)
                        <span wire:key="health-trend-label-{{ $loop->index }}">{{ $bar['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </flux:card>

        {{-- Breeding summary --}}
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Breeding summary') }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Matings over the last :n months', ['n' => $rangeMonths]) }}</flux:text>
                </div>
                <flux:icon.heart variant="micro" class="size-4 shrink-0 text-pink-600 dark:text-pink-400" />
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                <div class="rounded-lg bg-zinc-100/70 p-3 dark:bg-white/5">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Matings') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums">{{ number_format($this->breedingSummary['totalMatings']) }}</flux:heading>
                </div>
                <div class="rounded-lg bg-zinc-100/70 p-3 dark:bg-white/5">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Completed') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums">{{ number_format($this->breedingSummary['completedCount']) }}</flux:heading>
                </div>
                <div class="rounded-lg bg-pink-50 p-3 dark:bg-pink-500/10">
                    <flux:text size="sm" class="text-pink-600 dark:text-pink-400">{{ __('Success rate') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums text-pink-700 dark:text-pink-300">{{ number_format($this->breedingSummary['successRate'], 1) }}%</flux:heading>
                </div>
                <div class="rounded-lg bg-zinc-100/70 p-3 dark:bg-white/5">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Avg. offspring') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums">{{ $this->breedingSummary['averageOffspring'] !== null ? number_format($this->breedingSummary['averageOffspring'], 2) : '—' }}</flux:heading>
                </div>
            </div>

            <flux:separator />

            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Monthly trend') }}</flux:text>
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="mt-2 h-20 w-full overflow-visible" role="img" aria-label="{{ __('Bar chart of matings per month') }}">
                    <line x1="0" y1="98" x2="100" y2="98" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="0.5" />
                    @foreach ($this->breedingTrendBars as $bar)
                        <rect
                            wire:key="breeding-trend-{{ $loop->index }}"
                            x="{{ $bar['x'] }}"
                            y="{{ $bar['y'] }}"
                            width="{{ $bar['width'] }}"
                            height="{{ $bar['height'] }}"
                            rx="1"
                            class="fill-pink-500 dark:fill-pink-400"
                        >
                            <title>{{ $bar['label'] }}: {{ $bar['value'] }}</title>
                        </rect>
                    @endforeach
                </svg>
                <div class="mt-1 flex flex-wrap justify-between gap-x-2 text-[10px] text-zinc-500 dark:text-zinc-400">
                    @foreach ($this->breedingTrendBars as $bar)
                        <span wire:key="breeding-trend-label-{{ $loop->index }}">{{ $bar['label'] }}</span>
                    @endforeach
                </div>
            </div>
        </flux:card>

        {{-- Growth summary --}}
        <flux:card class="space-y-5">
            <div class="flex items-center justify-between">
                <div>
                    <flux:heading size="lg">{{ __('Growth summary') }}</flux:heading>
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Average weight over the last :n months', ['n' => $rangeMonths]) }}</flux:text>
                </div>
                <flux:icon.scale variant="micro" class="size-4 shrink-0 text-violet-600 dark:text-violet-400" />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-lg bg-zinc-100/70 p-3 dark:bg-white/5">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Overall average') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums">
                        {{ $this->growthSummary['overallAverageWeight'] !== null ? __(':weight kg', ['weight' => $this->growthSummary['overallAverageWeight']]) : '—' }}
                    </flux:heading>
                </div>
                <div class="rounded-lg bg-zinc-100/70 p-3 dark:bg-white/5">
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Weight logs') }}</flux:text>
                    <flux:heading size="lg" class="mt-1 font-mono tabular-nums">{{ number_format($this->growthSummary['totalWeightLogs']) }}</flux:heading>
                </div>
            </div>

            @if ($this->growthTrendHasData)
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-36 w-full overflow-visible" role="img" aria-label="{{ __('Line chart of average weight per month') }}">
                    <defs>
                        <linearGradient id="reportsGrowthAreaGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" class="[stop-color:var(--color-violet-400)] [stop-opacity:0.35]" />
                            <stop offset="100%" class="[stop-color:var(--color-violet-400)] [stop-opacity:0]" />
                        </linearGradient>
                    </defs>
                    <line x1="0" y1="98" x2="100" y2="98" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="0.5" />
                    @foreach ($this->growthTrendAreaPaths as $areaPath)
                        <path wire:key="growth-area-{{ $loop->index }}" d="{{ $areaPath }}" fill="url(#reportsGrowthAreaGradient)" stroke="none" />
                    @endforeach
                    @foreach ($this->growthTrendLinePaths as $linePath)
                        <path wire:key="growth-line-{{ $loop->index }}" d="{{ $linePath }}" fill="none" class="stroke-violet-500 dark:stroke-violet-400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    @endforeach
                    @foreach ($this->growthTrendPoints as $point)
                        @if ($point['y'] !== null)
                            <circle wire:key="growth-point-{{ $loop->index }}" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="1.5" class="fill-violet-500 dark:fill-violet-400">
                                <title>{{ $point['label'] }}: {{ __(':weight kg', ['weight' => $point['value']]) }}</title>
                            </circle>
                        @endif
                    @endforeach
                </svg>
                <div class="flex flex-wrap justify-between gap-x-2 text-xs text-zinc-500 dark:text-zinc-400">
                    @foreach ($this->growthTrendPoints as $point)
                        <span wire:key="growth-label-{{ $loop->index }}">{{ $point['label'] }}</span>
                    @endforeach
                </div>
            @else
                <div class="flex h-36 flex-col items-center justify-center gap-2">
                    <flux:icon.scale variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('No weight data recorded in this window.') }}</flux:text>
                </div>
            @endif
        </flux:card>
    </div>
</div>
