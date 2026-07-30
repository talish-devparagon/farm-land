<?php

namespace App\Actions\Reports;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Enums\HealthRecordType;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class GetHealthSummaryAction
{
    /**
     * Health record counts by type over the selected window, a per-month
     * trend, and the current overdue/upcoming counts (which always reflect
     * "right now", independent of the selected window).
     *
     * @return array{
     *     totalRecords: int,
     *     byType: array<string, int>,
     *     monthlyTrend: array<string, int>,
     *     overdueCount: int,
     *     upcomingCount: int,
     * }
     */
    public function handle(int $rangeMonths = 6): array
    {
        $months = (new GetRecentMonthsAction)->handle($rangeMonths);

        $records = HealthRecord::query()
            ->whereHas('animal')
            ->whereBetween('date', [$months->first()['start'], $months->last()['end']])
            ->get(['type', 'date']);

        return [
            'totalRecords' => $records->count(),
            'byType' => $this->byType($records),
            'monthlyTrend' => $this->monthlyTrend($records, $months),
            'overdueCount' => $this->overdueCount(),
            'upcomingCount' => $this->upcomingCount(),
        ];
    }

    /**
     * @param  Collection<int, HealthRecord>  $records
     * @return array<string, int>
     */
    private function byType(Collection $records): array
    {
        $counts = $records
            ->groupBy(fn (HealthRecord $record): string => $record->type->value)
            ->map(fn (Collection $group): int => $group->count())
            ->all();

        return array_merge(
            array_fill_keys(array_map(fn (HealthRecordType $type): string => $type->value, HealthRecordType::cases()), 0),
            $counts,
        );
    }

    /**
     * @param  Collection<int, HealthRecord>  $records
     * @param  \Illuminate\Support\Collection<int, array{label: string, start: Carbon, end: Carbon}>  $months
     * @return array<string, int>
     */
    private function monthlyTrend(Collection $records, $months): array
    {
        return $months->mapWithKeys(fn (array $month): array => [
            $month['label'] => $records->filter(fn (HealthRecord $record): bool => $record->date->between($month['start'], $month['end']))->count(),
        ])->all();
    }

    /**
     * Health records with a next_due_date already in the past.
     */
    private function overdueCount(): int
    {
        return HealthRecord::query()
            ->whereHas('animal')
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<', now()->toDateString())
            ->count();
    }

    /**
     * Health records due within the next 30 days.
     */
    private function upcomingCount(): int
    {
        return HealthRecord::query()
            ->whereHas('animal')
            ->whereBetween('next_due_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->count();
    }
}
