<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Concerns\ComputesLineChartGeometry;
use App\Models\Animal;
use App\Models\WeightLog;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class WeightTrendChart extends Component
{
    use ComputesLineChartGeometry;

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
        return $this->lineChartHasData($this->weightTrend());
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
        return $this->lineChartPoints($this->weightTrend());
    }

    /**
     * SVG `d` path attribute for the trend line, one string per contiguous segment.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function linePaths(): array
    {
        return $this->lineChartLinePaths($this->weightTrend());
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
        return $this->lineChartAreaPaths($this->weightTrend());
    }
}
