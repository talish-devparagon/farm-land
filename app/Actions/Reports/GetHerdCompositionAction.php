<?php

namespace App\Actions\Reports;

use App\Models\Animal;
use Illuminate\Support\Collection;

class GetHerdCompositionAction
{
    /**
     * Age bracket labels, in display order, keyed by their upper bound in
     * months. `null` represents "no upper bound" (i.e. the oldest bracket).
     *
     * @var array<string, int|null>
     */
    private const AGE_BRACKETS = [
        '0-6 months' => 6,
        '6-12 months' => 12,
        '1-2 years' => 24,
        '2+ years' => null,
    ];

    /**
     * Breakdown of every animal on the current farm by breed, status, sex,
     * and age bracket (computed from date_of_birth).
     *
     * @return array{
     *     total: int,
     *     byBreed: array<string, int>,
     *     byStatus: array<string, int>,
     *     bySex: array<string, int>,
     *     byAgeBracket: array<string, int>,
     * }
     */
    public function handle(): array
    {
        $animals = Animal::query()->get(['breed', 'sex', 'status', 'date_of_birth']);

        return [
            'total' => $animals->count(),
            'byBreed' => $this->byBreed($animals),
            'byStatus' => $this->byStatus($animals),
            'bySex' => $this->bySex($animals),
            'byAgeBracket' => $this->byAgeBracket($animals),
        ];
    }

    /**
     * @param  Collection<int, Animal>  $animals
     * @return array<string, int>
     */
    private function byBreed($animals): array
    {
        return $animals
            ->groupBy(fn (Animal $animal): string => $animal->breed ?: 'Unknown')
            ->map(fn ($group): int => $group->count())
            ->sortDesc()
            ->all();
    }

    /**
     * @param  Collection<int, Animal>  $animals
     * @return array<string, int>
     */
    private function byStatus($animals): array
    {
        return $animals
            ->groupBy(fn (Animal $animal): string => $animal->status->value)
            ->map(fn ($group): int => $group->count())
            ->all();
    }

    /**
     * @param  Collection<int, Animal>  $animals
     * @return array<string, int>
     */
    private function bySex($animals): array
    {
        return $animals
            ->groupBy(fn (Animal $animal): string => $animal->sex->value)
            ->map(fn ($group): int => $group->count())
            ->all();
    }

    /**
     * @param  Collection<int, Animal>  $animals
     * @return array<string, int>
     */
    private function byAgeBracket($animals): array
    {
        $brackets = array_fill_keys(array_keys(self::AGE_BRACKETS), 0);
        $brackets['Unknown'] = 0;

        foreach ($animals as $animal) {
            $brackets[$this->ageBracketFor($animal)]++;
        }

        return $brackets;
    }

    private function ageBracketFor(Animal $animal): string
    {
        if ($animal->date_of_birth === null) {
            return 'Unknown';
        }

        $ageInMonths = abs($animal->date_of_birth->diffInMonths(now()));

        foreach (self::AGE_BRACKETS as $label => $upperBoundMonths) {
            if ($upperBoundMonths === null || $ageInMonths < $upperBoundMonths) {
                return $label;
            }
        }

        return '2+ years';
    }
}
