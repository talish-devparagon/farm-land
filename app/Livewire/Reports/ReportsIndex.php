<?php

namespace App\Livewire\Reports;

use App\Actions\Reports\GetBreedingSummaryAction;
use App\Actions\Reports\GetGrowthSummaryAction;
use App\Actions\Reports\GetHealthSummaryAction;
use App\Actions\Reports\GetHerdCompositionAction;
use App\Concerns\ComputesDonutSegments;
use App\Models\Animal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Reports')]
class ReportsIndex extends Component
{
    use ComputesDonutSegments;

    /**
     * Number of months (including the current month) the report cuts cover.
     * Restricted to 3, 6, or 12; any other value is clamped back to 6.
     */
    #[Url]
    public int $rangeMonths = 6;

    /**
     * @var array<int, int>
     */
    private const ALLOWED_RANGES = [3, 6, 12];

    public function mount(): void
    {
        Gate::authorize('viewAny', Animal::class);

        if (! in_array($this->rangeMonths, self::ALLOWED_RANGES, true)) {
            $this->rangeMonths = 6;
        }
    }

    public function updatedRangeMonths(): void
    {
        if (! in_array($this->rangeMonths, self::ALLOWED_RANGES, true)) {
            $this->rangeMonths = 6;
        }
    }

    /**
     * @return array<int, int>
     */
    #[Computed]
    public function availableRanges(): array
    {
        return self::ALLOWED_RANGES;
    }

    /**
     * Breakdown of every animal on the current farm by breed, status, sex,
     * and age bracket. Not affected by rangeMonths — it reflects the herd
     * as it stands today.
     *
     * @return array{
     *     total: int,
     *     byBreed: array<string, int>,
     *     byStatus: array<string, int>,
     *     bySex: array<string, int>,
     *     byAgeBracket: array<string, int>,
     * }
     */
    #[Computed]
    public function herdComposition(): array
    {
        return app(GetHerdCompositionAction::class)->handle();
    }

    /**
     * @return array{
     *     totalRecords: int,
     *     byType: array<string, int>,
     *     monthlyTrend: array<string, int>,
     *     overdueCount: int,
     *     upcomingCount: int,
     * }
     */
    #[Computed]
    public function healthSummary(): array
    {
        return app(GetHealthSummaryAction::class)->handle($this->rangeMonths);
    }

    /**
     * @return array{
     *     totalMatings: int,
     *     completedCount: int,
     *     successRate: float,
     *     averageOffspring: float|null,
     *     monthlyTrend: array<string, int>,
     * }
     */
    #[Computed]
    public function breedingSummary(): array
    {
        return app(GetBreedingSummaryAction::class)->handle($this->rangeMonths);
    }

    /**
     * @return array{
     *     monthlyAverageWeight: array<string, float|null>,
     *     overallAverageWeight: float|null,
     *     totalWeightLogs: int,
     * }
     */
    #[Computed]
    public function growthSummary(): array
    {
        return app(GetGrowthSummaryAction::class)->handle($this->rangeMonths);
    }

    /**
     * Breed breakdown of the whole herd (all statuses, unlike the dashboard's
     * alive-only donut) bucketed into the top 4 breeds plus "Other" for the
     * Herd Composition donut chart.
     *
     * @return Collection<int, array{label: string, count: int, percent: float, color: string}>
     */
    #[Computed]
    public function herdBreedSegments(): Collection
    {
        return $this->donutSegments($this->herdComposition()['byBreed']);
    }

    /**
     * Male/female split of the herd, with each side's share of the total.
     *
     * @return array{male: int, female: int, malePercent: float, femalePercent: float}
     */
    #[Computed]
    public function herdSexSplit(): array
    {
        $bySex = $this->herdComposition()['bySex'];
        $male = $bySex['male'] ?? 0;
        $female = $bySex['female'] ?? 0;
        $total = max($male + $female, 1);

        return [
            'male' => $male,
            'female' => $female,
            'malePercent' => round($male / $total * 100, 1),
            'femalePercent' => round($female / $total * 100, 1),
        ];
    }

    /**
     * Age bracket breakdown as already-shaped bars, excluding empty brackets.
     *
     * @return Collection<int, array{label: string, count: int, percent: float}>
     */
    #[Computed]
    public function herdAgeBars(): Collection
    {
        $byAgeBracket = $this->herdComposition()['byAgeBracket'];
        $max = max([1, ...array_values($byAgeBracket)]);

        return collect($byAgeBracket)
            ->filter(fn (int $count): bool => $count > 0)
            ->map(fn (int $count, string $bracket): array => [
                'label' => $bracket,
                'count' => $count,
                'percent' => round($count / $max * 100, 1),
            ])
            ->values();
    }
}
