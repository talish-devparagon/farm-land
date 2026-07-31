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

                {{-- Breed distribution --}}
                <div>
                    <flux:text size="sm" class="mb-3 font-medium text-zinc-700 dark:text-zinc-300">{{ __('By breed') }}</flux:text>
                    <livewire:charts.donut-chart
                        :segments="$this->herdBreedSegments->all()"
                        :total="$this->herdComposition['total']"
                        total-label="{{ __('animals') }}"
                        :empty-message="__('No animals recorded yet.')"
                    />
                </div>

                <flux:separator />

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
                {{-- Chart.js rendering wired up by the frontend pass; see App\Livewire\Charts\BarChart. --}}
                <livewire:charts.bar-chart :series="$this->healthSummary['monthlyTrend']" color="emerald" height="h-20" />
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
                {{-- Chart.js rendering wired up by the frontend pass; see App\Livewire\Charts\BarChart. --}}
                <livewire:charts.bar-chart :series="$this->breedingSummary['monthlyTrend']" color="pink" height="h-20" />
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

            {{-- Reuses the dashboard's weight trend chart, parameterized by the selected range. --}}
            <livewire:dashboard.weight-trend-chart :months="$rangeMonths" />
        </flux:card>
    </div>
</div>
