<div>
    @if ($this->hasData())
        <div wire:ignore class="{{ $height ?? 'h-36' }} w-full relative" x-data="lineChart(@js($this->chartData), @js($color), @js($unit), @js($this->hasData()))" x-init="init()">
            <canvas x-ref="canvas" class="h-full w-full"></canvas>
        </div>
    @else
        <div class="flex h-40 flex-col items-center justify-center gap-2">
            <flux:icon.scale variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $emptyMessage ?: __('No data available.') }}</flux:text>
        </div>
    @endif
</div>
