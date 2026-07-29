<?php

namespace App\Livewire;

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard')]
class Dashboard extends Component
{
    /**
     * @return array{total: int, alive: int, sold: int, deceased: int, male: int, female: int, malePercent: int, femalePercent: int}
     */
    #[Computed]
    public function herdOverview(): array
    {
        $statusCounts = Animal::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $sexCounts = Animal::query()
            ->selectRaw('sex, count(*) as aggregate')
            ->groupBy('sex')
            ->pluck('aggregate', 'sex');

        $male = (int) ($sexCounts[AnimalSex::Male->value] ?? 0);
        $female = (int) ($sexCounts[AnimalSex::Female->value] ?? 0);
        $sexTotal = max(1, $male + $female);
        $malePercent = (int) round($male / $sexTotal * 100);

        return [
            'total' => (int) $statusCounts->sum(),
            'alive' => (int) ($statusCounts[AnimalStatus::Alive->value] ?? 0),
            'sold' => (int) ($statusCounts[AnimalStatus::Sold->value] ?? 0),
            'deceased' => (int) ($statusCounts[AnimalStatus::Deceased->value] ?? 0),
            'male' => $male,
            'female' => $female,
            'malePercent' => $malePercent,
            'femalePercent' => 100 - $malePercent,
        ];
    }
}
