<?php

namespace App\Actions\Reports;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Models\BreedingRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class GetBreedingSummaryAction
{
    /**
     * Breeding activity over the selected window: total matings, the
     * kidding completion/success rate, the average offspring count per
     * completed kidding, and a per-month mating trend.
     *
     * @return array{
     *     totalMatings: int,
     *     completedCount: int,
     *     successRate: float,
     *     averageOffspring: float|null,
     *     monthlyTrend: array<string, int>,
     * }
     */
    public function handle(int $rangeMonths = 6): array
    {
        $months = (new GetRecentMonthsAction)->handle($rangeMonths);

        $records = BreedingRecord::query()
            ->whereHas('doe')
            ->whereBetween('mating_date', [$months->first()['start'], $months->last()['end']])
            ->get(['mating_date', 'actual_kidding_date', 'number_of_offspring']);

        $totalMatings = $records->count();
        $completed = $records->whereNotNull('actual_kidding_date');
        $completedCount = $completed->count();

        $offspringValues = $completed->pluck('number_of_offspring')->filter(fn (?int $value): bool => $value !== null);

        return [
            'totalMatings' => $totalMatings,
            'completedCount' => $completedCount,
            'successRate' => $totalMatings > 0 ? round($completedCount / $totalMatings * 100, 2) : 0.0,
            'averageOffspring' => $offspringValues->isNotEmpty() ? round((float) $offspringValues->avg(), 2) : null,
            'monthlyTrend' => $this->monthlyTrend($records, $months),
        ];
    }

    /**
     * @param  Collection<int, BreedingRecord>  $records
     * @param  \Illuminate\Support\Collection<int, array{label: string, start: Carbon, end: Carbon}>  $months
     * @return array<string, int>
     */
    private function monthlyTrend(Collection $records, $months): array
    {
        return $months->mapWithKeys(fn (array $month): array => [
            $month['label'] => $records->filter(fn (BreedingRecord $record): bool => $record->mating_date->between($month['start'], $month['end']))->count(),
        ])->all();
    }
}
