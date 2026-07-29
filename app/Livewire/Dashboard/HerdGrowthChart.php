<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Models\Animal;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HerdGrowthChart extends Component
{
    /**
     * Number of new animals per month, keyed by month label, for the last 6 months.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function herdGrowth(): array
    {
        $months = app(GetRecentMonthsAction::class)->handle();

        $animals = Animal::query()
            ->whereBetween('created_at', [$months->first()['start'], $months->last()['end']])
            ->get(['id', 'created_at']);

        return $months->mapWithKeys(fn (array $month): array => [
            $month['label'] => $animals->filter(fn (Animal $animal): bool => $animal->created_at->between($month['start'], $month['end']))->count(),
        ])->all();
    }

    /**
     * Bar geometry for each month, ready for the view to render with zero further math.
     *
     * @return Collection<int, array{label: string, value: int, x: float, y: float, width: float, height: float}>
     */
    #[Computed]
    public function bars(): Collection
    {
        $growth = $this->herdGrowth();
        $growthMax = max(1, max($growth));
        $growthSlots = count($growth);

        return collect($growth)->values()->map(function (int $value, int $index) use ($growth, $growthSlots, $growthMax): array {
            $slot = 100 / max($growthSlots, 1);
            $barWidth = $slot * 0.5;
            $xCenter = $slot * $index + $slot / 2;
            $height = ($value / $growthMax) * 82;
            $barHeight = max($height, $value > 0 ? 2 : 0);

            return [
                'label' => array_keys($growth)[$index],
                'value' => $value,
                'x' => round($xCenter - $barWidth / 2, 2),
                'width' => round($barWidth, 2),
                'height' => round($barHeight, 2),
                'y' => round(98 - $barHeight, 2),
            ];
        });
    }
}
