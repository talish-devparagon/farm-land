<?php

namespace App\Livewire\Charts;

use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class BarChart extends Component
{
    /**
     * Labelled integer counts to render as bars.
     *
     * @var array<string, int>
     */
    #[Reactive]
    public array $series = [];

    public string $color = 'cyan';

    public string $height = 'h-36';

    /**
     * Chart.js-friendly labels and values derived from the series.
     *
     * @return array{labels: array<int, string>, values: array<int, int>}
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
            // BarChart instance on the page, so sibling charts (e.g. Health
            // vs. Breeding trend on the Reports page) would overwrite each
            // other's Chart.js data with the wrong series.
            $this->dispatch('chart-data-updated', chartData: $this->chartData())->self();
        }
    }
}
