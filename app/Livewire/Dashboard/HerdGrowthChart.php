<?php

namespace App\Livewire\Dashboard;

use App\Actions\Dashboard\GetRecentMonthsAction;
use App\Models\Animal;
use Livewire\Attributes\Computed;
use Livewire\Component;

class HerdGrowthChart extends Component
{
    /**
     * Number of months (including the current month) of history to chart.
     */
    public int $months = 6;

    /**
     * Number of new animals per month, keyed by month label, for the last N months.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function herdGrowth(): array
    {
        $months = app(GetRecentMonthsAction::class)->handle($this->months);

        $animals = Animal::query()
            ->whereBetween('created_at', [$months->first()['start'], $months->last()['end']])
            ->get(['id', 'created_at']);

        return $months->mapWithKeys(fn (array $month): array => [
            $month['label'] => $animals->filter(fn (Animal $animal): bool => $animal->created_at->between($month['start'], $month['end']))->count(),
        ])->all();
    }
}
