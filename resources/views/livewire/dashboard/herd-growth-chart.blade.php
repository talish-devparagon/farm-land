<flux:card class="space-y-4">
    <div class="flex items-center justify-between">
        <flux:heading size="lg">{{ __('Herd growth') }}</flux:heading>
        <flux:icon.arrow-trending-up variant="micro" class="size-4 text-cyan-600 dark:text-cyan-400" />
    </div>
    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('New intake over the last 6 months') }}</flux:text>
    <svg viewBox="0 0 100 100" preserveAspectRatio="none" class="h-36 w-full overflow-visible" role="img" aria-label="{{ __('Bar chart of new animal intake per month') }}">
        <defs>
            <linearGradient id="growthBarGradient" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" class="[stop-color:var(--color-cyan-400)]" />
                <stop offset="100%" class="[stop-color:var(--color-cyan-600)]" />
            </linearGradient>
        </defs>
        <line x1="0" y1="98" x2="100" y2="98" class="stroke-zinc-200 dark:stroke-white/10" stroke-width="0.5" />
        @foreach ($this->bars as $bar)
            <rect
                wire:key="growth-bar-{{ $loop->index }}"
                x="{{ $bar['x'] }}"
                y="{{ $bar['y'] }}"
                width="{{ $bar['width'] }}"
                height="{{ $bar['height'] }}"
                rx="1"
                fill="url(#growthBarGradient)"
                class="drop-shadow-sm drop-shadow-cyan-600 motion-safe:transition-opacity motion-safe:duration-300 hover:opacity-75"
            >
                <title>{{ $bar['label'] }}: {{ __(':count new', ['count' => $bar['value']]) }}</title>
            </rect>
        @endforeach
    </svg>
    <div class="flex justify-between text-xs text-zinc-500 dark:text-zinc-400">
        @foreach ($this->bars as $bar)
            <span wire:key="growth-label-{{ $loop->index }}">{{ $bar['label'] }}</span>
        @endforeach
    </div>
</flux:card>
