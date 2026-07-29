<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Weight trend') }}</flux:heading>
        <flux:icon.scale variant="micro" class="size-4 text-violet-600 dark:text-violet-400" />
    </div>
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Average weight over the last 6 months') }}</flux:text>

    @if ($this->hasData)
        <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-36 w-full overflow-visible" role="img" aria-label="{{ __('Line chart of average weight per month') }}">
            <defs>
                <linearGradient id="weightAreaGradient" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" class="[stop-color:var(--color-violet-400)] [stop-opacity:0.35]" />
                    <stop offset="100%" class="[stop-color:var(--color-violet-400)] [stop-opacity:0]" />
                </linearGradient>
            </defs>
            <line x1="0" y1="98" x2="100" y2="98" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="0.5" />
            @foreach ($this->areaPaths as $areaPath)
                <path wire:key="weight-area-{{ $loop->index }}" d="{{ $areaPath }}" fill="url(#weightAreaGradient)" stroke="none" />
            @endforeach
            @foreach ($this->linePaths as $linePath)
                <path wire:key="weight-line-{{ $loop->index }}" d="{{ $linePath }}" fill="none" class="stroke-violet-500 dark:stroke-violet-400" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            @endforeach
            @foreach ($this->points as $point)
                @if ($point['y'] !== null)
                    <circle wire:key="weight-point-{{ $loop->index }}" cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="1.5" class="fill-violet-500 dark:fill-violet-400">
                        <title>{{ $point['label'] }}: {{ __(':weight kg', ['weight' => $point['value']]) }}</title>
                    </circle>
                @endif
            @endforeach
        </svg>
        <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
            @foreach ($this->points as $point)
                <span wire:key="weight-label-{{ $loop->index }}">{{ $point['label'] }}</span>
            @endforeach
        </div>
    @else
        <div class="flex h-36 flex-col items-center justify-center gap-2">
            <flux:icon.scale variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('No weight data recorded yet.') }}</flux:text>
        </div>
    @endif
</flux:card>
