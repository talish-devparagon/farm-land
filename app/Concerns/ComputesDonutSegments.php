<?php

namespace App\Concerns;

use Illuminate\Support\Collection;

/**
 * Shared bucketing logic for rendering a donut chart from a labelled count
 * distribution, capping the number of distinct slices so the chart and its
 * legend stay readable.
 */
trait ComputesDonutSegments
{
    /**
     * Bucket a label=>count distribution into the top 4 entries plus an
     * "Other" bucket for the remainder, with a cycling color token assigned
     * to each (color tokens map to Tailwind color families, resolved to
     * actual colors by the frontend).
     *
     * @param  array<string, int>  $counts
     * @return Collection<int, array{label: string, count: int, percent: float, color: string}>
     */
    protected function donutSegments(array $counts): Collection
    {
        $entries = collect($counts);
        $displayEntries = $entries->take(4);
        $otherTotal = $entries->slice(4)->sum();

        if ($otherTotal > 0) {
            $displayEntries = $displayEntries->put('Other', $otherTotal);
        }

        $colors = ['indigo', 'amber', 'teal', 'rose'];
        $total = max(1, $displayEntries->sum());

        return $displayEntries->values()->map(function (int $count, int $index) use ($displayEntries, $colors, $total): array {
            $label = $displayEntries->keys()[$index];
            $color = $label === 'Other' ? 'zinc' : $colors[$index % count($colors)];

            return [
                'label' => $label,
                'count' => $count,
                'percent' => round($count / $total * 100, 2),
                'color' => $color,
            ];
        });
    }

    /**
     * Tailwind background-color utility classes for each color token, used
     * to color legend swatches next to a donut chart.
     *
     * @return array<string, string>
     */
    protected function donutSwatchClasses(): array
    {
        return [
            'indigo' => 'bg-indigo-500 dark:bg-indigo-400',
            'amber' => 'bg-amber-500 dark:bg-amber-400',
            'teal' => 'bg-teal-500 dark:bg-teal-400',
            'rose' => 'bg-rose-500 dark:bg-rose-400',
            'zinc' => 'bg-zinc-400 dark:bg-zinc-500',
        ];
    }
}
