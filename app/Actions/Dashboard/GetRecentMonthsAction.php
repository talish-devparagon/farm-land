<?php

namespace App\Actions\Dashboard;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetRecentMonthsAction
{
    /**
     * Build the last $months months (including the current month) as labelled date ranges.
     *
     * @return Collection<int, array{label: string, start: Carbon, end: Carbon}>
     */
    public function handle(int $months = 6): Collection
    {
        $currentMonthStart = now()->startOfMonth();

        return collect(range($months - 1, 0))->map(function (int $offset) use ($currentMonthStart): array {
            // Clone before subtracting months to avoid Carbon's day-overflow rollover
            // when subtracting months from a day like the 31st.
            $monthStart = $currentMonthStart->clone()->subMonths($offset);

            return [
                'label' => $monthStart->format('M'),
                'start' => $monthStart->clone(),
                'end' => $monthStart->clone()->endOfMonth(),
            ];
        })->values();
    }
}
