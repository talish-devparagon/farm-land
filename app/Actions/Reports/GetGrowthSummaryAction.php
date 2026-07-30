<?php

namespace App\Actions\Reports;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Models\WeightLog;

class GetGrowthSummaryAction
{
    /**
     * Weight/growth activity over the selected window: the average
     * recorded weight per month and the overall average and number of
     * weight logs across the whole window.
     *
     * `WeightLog` has no `farm_id`, so it must be scoped via `whereHas('animal')`.
     *
     * @return array{
     *     monthlyAverageWeight: array<string, float|null>,
     *     overallAverageWeight: float|null,
     *     totalWeightLogs: int,
     * }
     */
    public function handle(int $rangeMonths = 6): array
    {
        $months = (new GetRecentMonthsAction)->handle($rangeMonths);

        $weightLogs = WeightLog::query()
            ->whereHas('animal')
            ->whereBetween('recorded_at', [$months->first()['start'], $months->last()['end']])
            ->get(['weight', 'recorded_at']);

        return [
            'monthlyAverageWeight' => $months->mapWithKeys(function (array $month) use ($weightLogs): array {
                $logsInMonth = $weightLogs->filter(fn (WeightLog $log): bool => $log->recorded_at->between($month['start'], $month['end']));

                return [$month['label'] => $logsInMonth->isNotEmpty() ? round((float) $logsInMonth->avg('weight'), 2) : null];
            })->all(),
            'overallAverageWeight' => $weightLogs->isNotEmpty() ? round((float) $weightLogs->avg('weight'), 2) : null,
            'totalWeightLogs' => $weightLogs->count(),
        ];
    }
}
