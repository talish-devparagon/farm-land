<div wire:ignore class="{{ $height ?? 'h-36' }} w-full relative" x-data="barChart(@js($this->chartData), @js($color), @js($height ?? 'h-36'))" x-init="init()">
    <canvas x-ref="canvas" class="h-full w-full"></canvas>
</div>
