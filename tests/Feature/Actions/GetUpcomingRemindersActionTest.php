<?php

use App\Actions\GetUpcomingRemindersAction;
use App\Enums\AnimalSex;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\Farm;
use App\Models\HealthRecord;
use App\Models\User;

test('it returns health records with a next_due_date within the next 30 days', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $this->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();

    $dueSoon = HealthRecord::factory()->for($animal)->for($farm)->create([
        'next_due_date' => now()->addDays(10),
    ]);
    $tooFarOut = HealthRecord::factory()->for($animal)->for($farm)->create([
        'next_due_date' => now()->addDays(40),
    ]);
    $alreadyPast = HealthRecord::factory()->for($animal)->for($farm)->create([
        'next_due_date' => now()->subDays(5),
    ]);
    $noDueDate = HealthRecord::factory()->for($animal)->for($farm)->create([
        'next_due_date' => null,
    ]);

    $result = (new GetUpcomingRemindersAction)->handle();

    expect($result['healthRecords'])->toHaveCount(1)
        ->and($result['healthRecords']->first()->is($dueSoon))->toBeTrue()
        ->and($result['healthRecords']->contains($tooFarOut))->toBeFalse()
        ->and($result['healthRecords']->contains($alreadyPast))->toBeFalse()
        ->and($result['healthRecords']->contains($noDueDate))->toBeFalse();
});

test('it returns breeding records with an expected_kidding_date within the next 30 days and no actual_kidding_date', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $this->actingAs($owner->fresh());

    $doe = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);

    $dueSoon = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(15),
        'actual_kidding_date' => null,
    ]);
    $tooFarOut = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(60),
        'actual_kidding_date' => null,
    ]);
    $alreadyKidded = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(5),
        'actual_kidding_date' => now()->subDay(),
    ]);

    $result = (new GetUpcomingRemindersAction)->handle();

    expect($result['breedingRecords'])->toHaveCount(1)
        ->and($result['breedingRecords']->first()->is($dueSoon))->toBeTrue()
        ->and($result['breedingRecords']->contains($tooFarOut))->toBeFalse()
        ->and($result['breedingRecords']->contains($alreadyKidded))->toBeFalse();
});

test('it excludes health records belonging to a soft-deleted animal', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $this->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();
    $orphanedRecord = HealthRecord::factory()->for($animal)->for($farm)->create([
        'next_due_date' => now()->addDays(10),
    ]);
    $animal->delete();

    $result = (new GetUpcomingRemindersAction)->handle();

    expect($result['healthRecords'])->toHaveCount(0)
        ->and($result['healthRecords']->contains($orphanedRecord))->toBeFalse();
});

test('it excludes breeding records belonging to a soft-deleted doe', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $this->actingAs($owner->fresh());

    $doe = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);
    $orphanedRecord = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(15),
        'actual_kidding_date' => null,
    ]);
    $doe->delete();

    $result = (new GetUpcomingRemindersAction)->handle();

    expect($result['breedingRecords'])->toHaveCount(0)
        ->and($result['breedingRecords']->contains($orphanedRecord))->toBeFalse();
});

test('it does not return upcoming events from another farm', function () {
    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    HealthRecord::factory()->for($otherAnimal)->for($otherFarm)->create([
        'next_due_date' => now()->addDays(10),
    ]);

    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $this->actingAs($owner->fresh());

    $result = (new GetUpcomingRemindersAction)->handle();

    expect($result['healthRecords'])->toHaveCount(0);
});
