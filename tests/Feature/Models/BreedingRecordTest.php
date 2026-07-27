<?php

use App\Enums\AnimalSex;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\Farm;
use Carbon\CarbonInterface;

test('breeding record casts mating, expected and actual kidding dates', function () {
    $farm = Farm::factory()->create();
    $doe = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);
    $breedingRecord = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create();

    expect($breedingRecord->mating_date)->toBeInstanceOf(CarbonInterface::class)
        ->and($breedingRecord->expected_kidding_date)->toBeInstanceOf(CarbonInterface::class);
});

test('breeding record belongs to a farm, doe and buck', function () {
    $farm = Farm::factory()->create();
    $doe = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);
    $buck = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Male]);

    $breedingRecord = BreedingRecord::factory()
        ->for($farm)
        ->for($doe, 'doe')
        ->for($buck, 'buck')
        ->create();

    expect($breedingRecord->farm->id)->toBe($farm->id)
        ->and($breedingRecord->doe->id)->toBe($doe->id)
        ->and($breedingRecord->buck->id)->toBe($buck->id);
});

test('completed state sets actual_kidding_date 145 to 155 days after mating and 1 to 3 offspring', function () {
    $farm = Farm::factory()->create();
    $doe = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);
    $breedingRecord = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->completed()->create();

    $daysBetween = $breedingRecord->mating_date->diffInDays($breedingRecord->actual_kidding_date);

    expect($breedingRecord->actual_kidding_date)->not->toBeNull()
        ->and($daysBetween)->toBeGreaterThanOrEqual(145)
        ->and($daysBetween)->toBeLessThanOrEqual(155)
        ->and($breedingRecord->number_of_offspring)->toBeGreaterThanOrEqual(1)
        ->and($breedingRecord->number_of_offspring)->toBeLessThanOrEqual(3);
});
