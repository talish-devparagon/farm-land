<?php

namespace App\Concerns;

use Illuminate\Support\Collection;

/**
 * Shared geometry math for rendering a simple SVG bar chart from a series of
 * labelled integer counts, scaled so the tallest bar reaches 80% of the
 * available chart height.
 */
trait ComputesBarChartGeometry
{
    /**
     * @param  array<string, int>  $series
     * @return Collection<int, array{label: string, value: int, x: float, y: float, width: float, height: float}>
     */
    protected function barChartBars(array $series): Collection
    {
        $max = max([1, ...array_values($series)]);
        $slot = 100 / max(count($series), 1);

        return collect($series)->values()->map(function (int $value, int $index) use ($series, $max, $slot): array {
            $height = $value > 0 ? ($value / $max) * 80 : 0;
            $width = $slot * 0.6;

            return [
                'label' => array_keys($series)[$index],
                'value' => $value,
                'x' => round($slot * $index + $slot * 0.2, 2),
                'y' => round(98 - $height, 2),
                'width' => round($width, 2),
                'height' => round($height, 2),
            ];
        });
    }
}
