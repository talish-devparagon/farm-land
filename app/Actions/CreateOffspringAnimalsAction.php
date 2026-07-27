<?php

namespace App\Actions;

use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\BreedingRecord;

class CreateOffspringAnimalsAction
{
    /**
     * Create an animal record for each offspring recorded against a breeding record's kidding.
     *
     * @param  array<int, array{tag_number: string, name: ?string, sex: string}>  $offspring
     */
    public function handle(BreedingRecord $breedingRecord, array $offspring): void
    {
        foreach ($offspring as $child) {
            Animal::create([
                'tag_number' => $child['tag_number'],
                'name' => $child['name'] ?? null,
                'sex' => $child['sex'],
                'date_of_birth' => $breedingRecord->actual_kidding_date,
                'mother_id' => $breedingRecord->doe_id,
                'father_id' => $breedingRecord->buck_id,
                'status' => AnimalStatus::Alive,
            ]);
        }
    }
}
