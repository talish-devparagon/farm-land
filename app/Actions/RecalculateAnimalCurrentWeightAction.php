<?php

namespace App\Actions;

use App\Models\Animal;

class RecalculateAnimalCurrentWeightAction
{
    /**
     * Update the animal's current weight to its latest recorded weight log.
     */
    public function handle(Animal $animal): void
    {
        $latestWeight = $animal->weightLogs()->latest('recorded_at')->value('weight');

        $animal->update(['current_weight' => $latestWeight]);
    }
}
