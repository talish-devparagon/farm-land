<?php

namespace App\Livewire\Dashboard;

use App\Concerns\ComputesDonutSegments;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BreedDistributionChart extends Component
{
    use ComputesDonutSegments;

    /**
     * Count of alive animals per breed, sorted descending, keyed by breed name.
     *
     * @return array<string, int>
     */
    #[Computed]
    public function breedDistribution(): array
    {
        $counts = Animal::query()
            ->where('status', AnimalStatus::Alive)
            ->select('breed')
            ->selectRaw('count(*) as aggregate')
            ->groupBy('breed')
            ->get()
            ->reduce(function (array $carry, Animal $animal): array {
                $aggregate = $animal->getAttribute('aggregate');
                $breed = $animal->breed ?: 'Unknown';
                $carry[$breed] = ($carry[$breed] ?? 0) + $aggregate;

                return $carry;
            }, []);

        return collect($counts)->sortDesc()->all();
    }

    /**
     * Total number of alive animals represented in the donut.
     */
    #[Computed]
    public function total(): int
    {
        return (int) Animal::query()->where('status', AnimalStatus::Alive)->count();
    }

    /**
     * Donut segments capped at the top 4 breeds plus an "Other" bucket, with
     * the color token needed to color the arc and its legend swatch.
     *
     * @return Collection<int, array{label: string, count: int, percent: float, color: string}>
     */
    #[Computed]
    public function segments(): Collection
    {
        return $this->donutSegments($this->breedDistribution());
    }
}
