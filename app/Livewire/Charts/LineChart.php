<?php

namespace App\Livewire\Charts;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class LineChart extends Component
{
    /**
     * Labelled values to render as a trend line. A null value means "no data
     * for that entry", which the frontend uses to render a gap in the line.
     *
     * @var array<string, float|null>
     */
    #[Reactive]
    public array $series = [];

    public string $color = 'violet';

    public string $height = 'h-36';

    public string $unit = '';

    public string $emptyMessage = '';

    /**
     * Whether at least one entry in the series has a non-null value.
     */
    #[Computed]
    public function hasData(): bool
    {
        return collect($this->series)->contains(fn (?float $value): bool => $value !== null);
    }

    /**
     * Chart.js-friendly labels and values derived from the series.
     *
     * @return array{labels: array<int, string>, values: array<int, float|null>}
     */
    #[Computed]
    public function chartData(): array
    {
        return [
            'labels' => array_keys($this->series),
            'values' => array_values($this->series),
        ];
    }

    public function updated(string $property): void
    {
        if ($property === 'series') {
            // ->self() is required: an unscoped dispatch() reaches every
            // LineChart instance on the page, which would let sibling
            // charts overwrite each other's Chart.js data.
            $this->dispatch('chart-data-updated', chartData: $this->chartData(), hasData: $this->hasData())->self();
        }
    }
}
