<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Weight Logs') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Track growth across the whole herd.') }}</flux:text>
    </div>

    <flux:card class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading size="lg">{{ __('Herd average trend') }}</flux:heading>
            <flux:icon.scale variant="micro" class="size-4 text-violet-600 dark:text-violet-400" />
        </div>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Average weight over the last 6 months') }}</flux:text>

        @if ($this->herdTrendHasData)
            <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-36 w-full overflow-visible" role="img" aria-label="{{ __('Line chart of average herd weight per month') }}">
                <defs>
                    <linearGradient id="herdWeightAreaGradient" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" class="[stop-color:var(--color-violet-400)] [stop-opacity:0.35]" />
                        <stop offset="100%" class="[stop-color:var(--color-violet-400)] [stop-opacity:0]" />
                    </linearGradient>
                </defs>
                <line x1="0" y1="98" x2="100" y2="98" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="0.5" />
                @foreach ($this->herdTrendAreaPaths as $areaPath)
                    <path wire:key="herd-weight-area-{{ $loop->index }}" d="{{ $areaPath }}" fill="url(#herdWeightAreaGradient)" stroke="none" />
                @endforeach
                @foreach ($this->herdTrendLinePaths as $linePath)
                    <path wire:key="herd-weight-line-{{ $loop->index }}" d="{{ $linePath }}" fill="none" class="stroke-violet-500 dark:stroke-violet-400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                @endforeach
                @foreach ($this->herdTrendPoints as $point)
                    @if ($point['y'] !== null)
                        <circle wire:key="herd-weight-point-{{ $loop->index }}" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="1.5" class="fill-violet-500 dark:fill-violet-400">
                            <title>{{ $point['label'] }}: {{ __(':weight kg', ['weight' => $point['value']]) }}</title>
                        </circle>
                    @endif
                @endforeach
            </svg>
            <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
                @foreach ($this->herdTrendPoints as $point)
                    <span wire:key="herd-weight-label-{{ $loop->index }}">{{ $point['label'] }}</span>
                @endforeach
            </div>
        @else
            <div class="flex h-36 flex-col items-center justify-center gap-2">
                <flux:icon.scale variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('No weight data recorded yet.') }}</flux:text>
            </div>
        @endif
    </flux:card>

    <div class="flex flex-wrap items-center gap-3">
        <flux:input
            wire:model.live="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search by tag number or name') }}"
            class="max-w-xs"
        />

        <flux:select wire:model.live="animalId" placeholder="{{ __('All animals') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All animals') }}</flux:select.option>
            @foreach ($this->animalOptions as $animal)
                <flux:select.option value="{{ $animal->id }}">{{ $animal->tag_number }}{{ $animal->name ? ' — '.$animal->name : '' }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model.live="dateFrom" type="date" :label="__('From')" class="max-w-xs" />
        <flux:input wire:model.live="dateTo" type="date" :label="__('To')" class="max-w-xs" />
    </div>

    @if ($this->weightLogs->isEmpty())
        <flux:callout icon="scale" heading="{{ __('No weight logs found') }}">
            <flux:callout.text>
                {{ __('Try adjusting your search or filters, or log a weight from an animal\'s profile.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Animal') }}</flux:table.column>
                <flux:table.column>{{ __('Weight') }}</flux:table.column>
                <flux:table.column>{{ __('Recorded date') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->weightLogs as $log)
                    <flux:table.row :key="'weight-log-'.$log->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('animals.show', $log->animal) }}" wire:navigate>
                                {{ $log->animal->tag_number }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ __(':weight kg', ['weight' => $log->weight]) }}</flux:table.cell>
                        <flux:table.cell>{{ $log->recorded_at->toFormattedDateString() }}</flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->weightLogs->links() }}
        </div>
    @endif
</div>
