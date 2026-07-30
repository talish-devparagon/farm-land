<?php

namespace App\Livewire\WeightLogs;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Concerns\ComputesLineChartGeometry;
use App\Models\Animal;
use App\Models\WeightLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Weight Logs')]
class WeightLogsIndex extends Component
{
    use ComputesLineChartGeometry, WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $animalId = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Animal::class);
    }

    /**
     * @return LengthAwarePaginator<int, WeightLog>
     */
    #[Computed]
    public function weightLogs(): LengthAwarePaginator
    {
        return WeightLog::query()
            ->with('animal')
            ->whereHas('animal', function ($query): void {
                $query->when($this->search, fn ($query) => $query->where(function ($query): void {
                    $query->where('tag_number', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                }));
            })
            ->when($this->animalId, fn ($query) => $query->where('animal_id', $this->animalId))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('recorded_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('recorded_at', '<=', $this->dateTo))
            ->orderByDesc('recorded_at')
            ->paginate(15);
    }

    /**
     * Animal picker dropdown options, restricted to the current farm.
     *
     * @return Collection<int, Animal>
     */
    #[Computed]
    public function animalOptions(): Collection
    {
        return Animal::query()
            ->orderBy('tag_number')
            ->get(['id', 'tag_number', 'name']);
    }

    /**
     * Average herd weight per month, keyed by month label, for the last 6 months.
     *
     * @return array<string, float|null>
     */
    #[Computed]
    public function herdAverageTrend(): array
    {
        $months = (new GetRecentMonthsAction)->handle();

        $weightLogs = WeightLog::query()
            ->whereHas('animal')
            ->whereBetween('recorded_at', [$months->first()['start'], $months->last()['end']])
            ->get(['weight', 'recorded_at']);

        return $months->mapWithKeys(function (array $month) use ($weightLogs): array {
            $logsInMonth = $weightLogs->filter(fn (WeightLog $log): bool => $log->recorded_at->between($month['start'], $month['end']));

            return [$month['label'] => $logsInMonth->isNotEmpty() ? round((float) $logsInMonth->avg('weight'), 2) : null];
        })->all();
    }

    /**
     * Whether at least one month has a recorded (non-null) herd average weight.
     */
    #[Computed]
    public function herdTrendHasData(): bool
    {
        return $this->lineChartHasData($this->herdAverageTrend());
    }

    /**
     * Chart-space points for the herd average trend. See {@see ComputesLineChartGeometry}.
     *
     * @return Collection<int, array{label: string, value: float|null, x: float, y: float|null}>
     */
    #[Computed]
    public function herdTrendPoints(): Collection
    {
        return $this->lineChartPoints($this->herdAverageTrend());
    }

    /**
     * SVG `d` path attribute(s) for the herd average trend line.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function herdTrendLinePaths(): array
    {
        return $this->lineChartLinePaths($this->herdAverageTrend());
    }

    /**
     * SVG `d` path attribute(s) for the herd average trend area fill.
     *
     * @return array<int, string>
     */
    #[Computed]
    public function herdTrendAreaPaths(): array
    {
        return $this->lineChartAreaPaths($this->herdAverageTrend());
    }
}
