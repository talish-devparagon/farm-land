<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Models\Animal;
use App\Models\WeightLog;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeightTrendChart extends Component
{
    /**
     * Number of months (including the current month) of history to chart.
     */
    public int $months = 6;

    /**
     * Average recorded weight per month, keyed by month label, for the last N months.
     * Falls back to the herd's current average weight, spread flat across every
     * month, when no weight logs have been recorded yet.
     *
     * @return array<string, float|null>
     */
    #[Computed]
    public function weightTrend(): array
    {
        $months = (new GetRecentMonthsAction)->handle($this->months);

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
}
