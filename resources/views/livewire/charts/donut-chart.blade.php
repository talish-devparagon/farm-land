<div>
    @if (count($segments) === 0)
        <div class="flex h-40 flex-col items-center justify-center gap-2">
            <flux:icon.chart-pie variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $emptyMessage ?: __('No data available.') }}</flux:text>
        </div>
    @else
        <div class="flex flex-col md:flex-row flex-wrap items-center md:justify-around gap-6">
            {{-- Chart canvas with centered total overlay --}}
            <div class="relative h-48">
                <div wire:ignore class="h-full w-full" x-data="donutChart(@js($this->chartData))" x-init="init()">
                    <canvas x-ref="canvas" class="h-full w-full"></canvas>
                </div>
                {{-- Centered total display --}}
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <flux:heading size="xl" class="font-mono tabular-nums">{{ number_format($total) }}</flux:heading>
                    @if ($totalLabel)
                        <flux:text size="xs" class="text-zinc-500 dark:text-zinc-400">{{ $totalLabel }}</flux:text>
                    @endif
                </div>
            </div>

            {{-- Legend --}}
            <ul class="max-w-max space-y-2">
                @forelse ($segments as $segment)
                    <li class="flex items-center justify-between gap-3 text-sm">
                        <div class="flex items-center gap-2.5">
                            <div class="size-2.5 rounded-full {{ $this->donutSwatchClasses()[$segment['color']] }}"></div>
                            <span class="text-zinc-700 dark:text-zinc-300">{{ $segment['label'] }}</span>
                        </div>
                        <span class="font-mono text-zinc-500 tabular-nums dark:text-zinc-400">{{ number_format($segment['count']) }}</span>
                    </li>
                @empty
                @endforelse
            </ul>
        </div>
    @endif
</div>
