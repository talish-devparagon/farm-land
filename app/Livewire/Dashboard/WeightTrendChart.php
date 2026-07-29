<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Models\Animal;
use App\Models\WeightLog;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeightTrendChart extends Component
{
    /**
     * Average recorded weight per month, keyed by month label, for the last 6 months.
     * Falls back to the herd's current average weight, spread flat across every
     * month, when no weight logs have been recorded yet.
     *
     * @return array<string, float|null>
     */
    #[Computed]
    public function weightTrend(): array
    {
        $months = (new GetRecentMonthsAction)->handle();

        $weightLogs = WeightLog::query()
            ->whereHas('animal')
            ->whereBetween('recorded_at', [$months->first()['start'], $months->last()['end']])
            ->get(['weight', 'recorded_at']);

        if ($weightLogs->isEmpty()) {
            $averageCurrentWeight = Animal::query()->whereNotNull('current_weight')->avg('current_weight');
            $averageCurrentWeight = $averageCurrentWeight !== null ? round((float) $averageCurrentWeight, 2) : null;

            return $months->mapWithKeys(fn (array $month): array => [$month['label'] => $averageCurrentWeight])->all();
        }

        return $months->mapWithKeys(function (array $month) use ($weightLogs): array {
            $logsInMonth = $weightLogs->filter(fn (WeightLog $log): bool => $log->recorded_at->between($month['start'], $month['end']));

            return [$month['label'] => $logsInMonth->isNotEmpty() ? round((float) $logsInMonth->avg('weight'), 2) : null];
        })->all();
    }

    /**
     * Whether at least one month has a recorded (non-null) average weight.
     */
    #[Computed]
    public function hasData(): bool
    {
        return collect($this->weightTrend())->contains(fn (?float $value): bool => $value !== null);
    }

    /**
     * Chart-space points for each month. `y` is null for months with no data,
     * which is what splits the line/area into separate segments across gaps.
     *
     * @return Collection<int, array{label: string, value: float|null, x: float, y: float|null}>
     */
    #[Computed]
    public function points(): Collection
    {
        $weights = $this->weightTrend();

        if (! $this->hasData()) {
            return collect();
        }

        $weightValues = collect($weights)->filter(fn (?float $value): bool => $value !== null);
        $min = $weightValues->min();
        $max = $weightValues->max();
        $range = max($max - $min, 1);
        $count = count($weights);
        $slot = 100 / max($count - 1, 1);

        return collect($weights)->values()->map(function (?float $value, int $index) use ($slot, $min, $range, $weights): array {
            return [
                'label' => array_keys($weights)[$index],
                'value' => $value,
                'x' => round($slot * $index, 2),
                'y' => $value !== null ? round(90 - (($value - $min) / $range) * 75, 2) : null,
            ];
        });
    }

    /**
     * Contiguous runs of points with data, split wherever a month has no data,
     * so the line/area paths don't falsely connect across a gap.
     *
     * @return Collection<int, Collection<int, array{label: string, value: float|null, x: float, y: float|null}>>
     */
    private function segments(): Collection
    {
        $segments = collect();
        $current = collect();

        foreach ($this->points() as $point) {
            if ($point['y'] !== null) {
                $current->push($point);
            } elseif ($current->isNotEmpty()) {
                $segments->push($current);
                $current = collect();
            }
        }

        if ($current->isNotEmpty()) {
            $segments->push($current);
        }

        return $segments;
    }

    /**
     * SVG `d` path attribute for the trend line, one string per contiguous segment.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function linePaths(): array
    {
        return $this->segments()
            ->map(fn (Collection $segment): string => $segment->values()
                ->map(fn (array $point, int $index): string => ($index === 0 ? 'M' : 'L').$point['x'].','.$point['y'])
                ->implode(' '))
            ->all();
    }

    /**
     * SVG `d` path attribute for the filled area beneath the trend line, one
     * string per contiguous segment.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function areaPaths(): array
    {
        return $this->segments()->map(function (Collection $segment): string {
            $line = $segment->map(fn (array $point): string => 'L'.$point['x'].','.$point['y'])->implode(' ');

            return 'M'.$segment->first()['x'].',98 '.$line.' L'.$segment->last()['x'].',98 Z';
        })->all();
    }
}
