<?php

namespace App\Livewire\Charts;

use App\Concerns\ComputesDonutSegments;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class DonutChart extends Component
{
    use ComputesDonutSegments;

    /**
     * Already-bucketed segments, shaped like the output of
     * `App\Concerns\ComputesDonutSegments::donutSegments()` cast to an array.
     *
     * @var array<int, array{label: string, count: int, percent: float, color: string}>
     */
    #[Reactive]
    public array $segments = [];

    public int $total = 0;

    public string $totalLabel = '';

    public string $emptyMessage = '';

    /**
     * Chart.js-friendly labels, values, and color tokens derived from the segments.
     *
     * @return array{labels: array<int,string>, values: array<int,int>, colorTokens: array<int,string>}
     */
    #[Computed]
    public function chartData(): array
    {
        return [
            'labels' => array_column($this->segments, 'label'),
            'values' => array_column($this->segments, 'count'),
            'colorTokens' => array_column($this->segments, 'color'),
        ];
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['segments', 'total'], true)) {
            // ->self() is required: an unscoped dispatch() reaches every
            // DonutChart instance on the page, which would let sibling
            // charts overwrite each other's Chart.js data.
            $this->dispatch('chart-data-updated', chartData: $this->chartData(), total: $this->total)->self();
        }
    }
}
