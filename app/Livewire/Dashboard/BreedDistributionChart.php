<?php

namespace App\Livewire\Dashboard;

use App\Enums\AnimalStatus;
use App\Models\Animal;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BreedDistributionChart extends Component
{
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
     * Donut segments capped at the top 4 breeds plus an "Other" bucket, fully
     * shaped with the stroke-dashoffset needed to draw each arc starting at
     * 12 o'clock (no CSS rotation should be applied alongside this) and the
     * Tailwind classes needed to color the arc and its legend swatch.
     *
     * @return Collection<int, array{label: string, count: int, percent: float, dashoffset: float, color: string, strokeClass: string, swatchClass: string}>
     */
    #[Computed]
    public function segments(): Collection
    {
        $breeds = collect($this->breedDistribution());
        $displayBreeds = $breeds->take(4);
        $otherTotal = $breeds->slice(4)->sum();

        if ($otherTotal > 0) {
            $displayBreeds = $displayBreeds->put('Other', $otherTotal);
        }

        $breedColors = ['indigo', 'amber', 'teal', 'rose'];
        $breedTotal = max(1, $displayBreeds->sum());
        $cumulativePercent = 0;

        return $displayBreeds->values()->map(function (int $count, int $index) use ($displayBreeds, $breedColors, $breedTotal, &$cumulativePercent): array {
            $label = $displayBreeds->keys()[$index];
            $percent = round($count / $breedTotal * 100, 2);
            $color = $label === 'Other' ? 'zinc' : $breedColors[$index % count($breedColors)];

            $segment = [
                'label' => $label,
                'count' => $count,
                'percent' => $percent,
                'dashoffset' => 125 - $cumulativePercent,
                'color' => $color,
                'strokeClass' => $this->strokeClasses()[$color],
                'swatchClass' => $this->swatchClasses()[$color],
            ];

            $cumulativePercent += $percent;

            return $segment;
        });
    }

    /**
     * @return array<string, string>
     */
    private function strokeClasses(): array
    {
        return [
            'indigo' => 'stroke-indigo-500 dark:stroke-indigo-400',
            'amber' => 'stroke-amber-500 dark:stroke-amber-400',
            'teal' => 'stroke-teal-500 dark:stroke-teal-400',
            'rose' => 'stroke-rose-500 dark:stroke-rose-400',
            'zinc' => 'stroke-zinc-400 dark:stroke-zinc-500',
        ];
    }

    /**
     * @return array<string, string>
     */
    private function swatchClasses(): array
    {
        return [
            'indigo' => 'bg-indigo-500 dark:bg-indigo-400',
            'amber' => 'bg-amber-500 dark:bg-amber-400',
            'teal' => 'bg-teal-500 dark:bg-teal-400',
            'rose' => 'bg-rose-500 dark:bg-rose-400',
            'zinc' => 'bg-zinc-400 dark:bg-zinc-500',
        ];
    }
}
