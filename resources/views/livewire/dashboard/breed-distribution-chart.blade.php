<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Breed distribution') }}</flux:heading>
        <flux:icon.chart-pie variant="micro" class="size-4 text-indigo-600 dark:text-indigo-400" />
    </div>
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Alive animals by breed') }}</flux:text>

    @if ($this->segments->isNotEmpty())
        <div class="flex items-center gap-6">
            <svg viewBox="0 0 100 100" class="size-32 shrink-0" role="img" aria-label="{{ __('Donut chart of alive animals by breed') }}">
                <circle cx="50" cy="50" r="40" fill="none" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="16" />
                @foreach ($this->segments as $segment)
                    <circle
                        wire:key="breed-segment-{{ $loop->index }}"
                        cx="50"
                        cy="50"
                        r="40"
                        fill="none"
                        stroke-width="16"
                        pathLength="100"
                        stroke-dasharray="{{ $segment['percent'] }} 100"
                        stroke-dashoffset="{{ $segment['dashoffset'] }}"
                        class="{{ $segment['strokeClass'] }}"
                    >
                        <title>{{ $segment['label'] }}: {{ __(':count (:percent%)', ['count' => $segment['count'], 'percent' => $segment['percent']]) }}</title>
                    </circle>
                @endforeach
                <text x="50" y="47" text-anchor="middle" class="fill-zinc-900 font-mono text-[20px] font-semibold tabular-nums dark:fill-white">{{ $this->total }}</text>
                <text x="50" y="62" text-anchor="middle" class="fill-zinc-500 text-[8px] dark:fill-zinc-400">{{ __('alive') }}</text>
            </svg>
            <ul class="min-w-0 flex-1 space-y-2">
                @foreach ($this->segments as $segment)
                    <li wire:key="breed-legend-{{ $loop->index }}" class="flex items-center justify-between gap-2 text-sm">
                        <span class="flex min-w-0 items-center gap-2">
                            <span class="size-2.5 shrink-0 rounded-full {{ $segment['swatchClass'] }}"></span>
                            <span class="truncate text-zinc-700 dark:text-zinc-300">{{ $segment['label'] }}</span>
                        </span>
                        <span class="shrink-0 font-mono text-zinc-500 tabular-nums dark:text-zinc-400">{{ $segment['count'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="flex h-32 flex-col items-center justify-center gap-2">
            <flux:icon.chart-pie variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('No alive animals to chart yet.') }}</flux:text>
        </div>
    @endif
</flux:card>
