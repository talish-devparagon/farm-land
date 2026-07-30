<?php

namespace App\Concerns;

use Illuminate\Support\Collection;

/**
 * Shared geometry math for rendering a simple SVG line/area trend chart from
 * a series of labelled values, some of which may be null (no data for that
 * entry). Extracted from App\Livewire\Dashboard\WeightTrendChart so every
 * Livewire component rendering this chart shape computes it the same way,
 * instead of duplicating the math in Blade `@php` blocks.
 */
trait ComputesLineChartGeometry
{
    /**
     * Whether at least one entry in the series has a non-null value.
     *
     * @param  array<string, float|null>  $series
     */
    protected function lineChartHasData(array $series): bool
    {
        return collect($series)->contains(fn (?float $value): bool => $value !== null);
    }

    /**
     * Chart-space points for each entry. `y` is null for entries with no
     * data, which is what splits the line/area into separate segments
     * across gaps.
     *
     * @param  array<string, float|null>  $series
     * @return Collection<int, array{label: string, value: float|null, x: float, y: float|null}>
     */
    protected function lineChartPoints(array $series): Collection
    {
        if (! $this->lineChartHasData($series)) {
            return collect();
        }

        $values = collect($series)->filter(fn (?float $value): bool => $value !== null);
        $min = $values->min();
        $max = $values->max();
        $range = max($max - $min, 1);
        $count = count($series);
        $slot = 100 / max($count - 1, 1);

        return collect($series)->values()->map(function (?float $value, int $index) use ($slot, $min, $range, $series): array {
            return [
                'label' => array_keys($series)[$index],
                'value' => $value,
                'x' => round($slot * $index, 2),
                'y' => $value !== null ? round(90 - (($value - $min) / $range) * 75, 2) : null,
            ];
        });
    }

    /**
     * Contiguous runs of points with data, split wherever an entry has no
     * data, so the line/area paths don't falsely connect across a gap.
     *
     * @param  array<string, float|null>  $series
     * @return Collection<int, Collection<int, array{label: string, value: float|null, x: float, y: float|null}>>
     */
    private function lineChartSegments(array $series): Collection
    {
        $segments = collect();
        $current = collect();

        foreach ($this->lineChartPoints($series) as $point) {
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
     * @param  array<string, float|null>  $series
     * @return array<int, string>
     */
    protected function lineChartLinePaths(array $series): array
    {
        return $this->lineChartSegments($series)
            ->map(fn (Collection $segment): string => $segment->values()
                ->map(fn (array $point, int $index): string => ($index === 0 ? 'M' : 'L').$point['x'].','.$point['y'])
                ->implode(' '))
            ->all();
    }

    /**
     * SVG `d` path attribute for the filled area beneath the trend line, one
     * string per contiguous segment.
     *
     * @param  array<string, float|null>  $series
     * @return array<int, string>
     */
    protected function lineChartAreaPaths(array $series): array
    {
        return $this->lineChartSegments($series)->map(function (Collection $segment): string {
            $line = $segment->map(fn (array $point): string => 'L'.$point['x'].','.$point['y'])->implode(' ');

            return 'M'.$segment->first()['x'].',98 '.$line.' L'.$segment->last()['x'].',98 Z';
        })->all();
    }
}
