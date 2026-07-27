<?php

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\HealthRecord;
use App\Models\WeightLog;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;

test('animal casts attributes and defaults status to alive', function () {
    $animal = Animal::factory()->create(['sex' => AnimalSex::Female]);

    expect($animal->sex)->toBeInstanceOf(AnimalSex::class)
        ->and($animal->sex)->toBe(AnimalSex::Female)
        ->and($animal->status)->toBeInstanceOf(AnimalStatus::class)
        ->and($animal->status)->toBe(AnimalStatus::Alive)
        ->and($animal->date_of_birth)->toBeInstanceOf(CarbonInterface::class)
        ->and($animal->current_weight)->toBeString();
});

test('animal belongs to a farm', function () {
    $farm = Farm::factory()->create();
    $animal = Animal::factory()->for($farm)->create();

    expect($animal->farm)->toBeInstanceOf(Farm::class)
        ->and($animal->farm->id)->toBe($farm->id);
});

test('animal has mother and father relations', function () {
    $mother = Animal::factory()->create(['sex' => AnimalSex::Female]);
    $father = Animal::factory()->create(['sex' => AnimalSex::Male]);
    $offspring = Animal::factory()->create([
        'mother_id' => $mother->id,
        'father_id' => $father->id,
    ]);

    expect($offspring->mother->id)->toBe($mother->id)
        ->and($offspring->father->id)->toBe($father->id);
});

test('offspring merges and sorts both parent relations by date of birth descending', function () {
    $mother = Animal::factory()->create(['sex' => AnimalSex::Female]);
    $father = Animal::factory()->create(['sex' => AnimalSex::Male]);

    $older = Animal::factory()->create([
        'mother_id' => $mother->id,
        'date_of_birth' => now()->subYear(),
    ]);
    $newer = Animal::factory()->create([
        'father_id' => $father->id,
        'date_of_birth' => now(),
    ]);

    $motherOffspring = $mother->offspring();
    expect($motherOffspring)->toHaveCount(1)
        ->and($motherOffspring->first()->id)->toBe($older->id);

    $fatherOffspring = $father->offspring();
    expect($fatherOffspring)->toHaveCount(1)
        ->and($fatherOffspring->first()->id)->toBe($newer->id);
});

test('animal has weight logs, health records and breeding record relations', function () {
    $animal = Animal::factory()->create(['sex' => AnimalSex::Female]);
    WeightLog::factory()->for($animal)->create();
    HealthRecord::factory()->for($animal)->for($animal->farm)->create();

    expect($animal->weightLogs()->count())->toBe(1)
        ->and($animal->healthRecords()->count())->toBe(1)
        ->and($animal->breedingRecordsAsDoe())->toBeInstanceOf(HasMany::class)
        ->and($animal->breedingRecordsAsBuck())->toBeInstanceOf(HasMany::class);
});

test('animal is soft deletable', function () {
    $animal = Animal::factory()->create();

    $animal->delete();

    expect(Animal::find($animal->id))->toBeNull()
        ->and(Animal::withTrashed()->find($animal->id))->not->toBeNull();
});

test('tag number is unique per farm', function () {
    $farm = Farm::factory()->create();
    Animal::factory()->for($farm)->create(['tag_number' => 'TAG-0001']);

    expect(fn () => Animal::factory()->for($farm)->create(['tag_number' => 'TAG-0001']))
        ->toThrow(QueryException::class);
});
