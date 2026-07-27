<?php

use App\Enums\AnimalSex;
use App\Enums\HealthRecordType;
use App\Livewire\Upcoming;
use App\Models\Animal;
use Livewire\Livewire;

test('upcoming page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('upcoming.index'))->assertOk();
});

test('upcoming lists health records due within the next 30 days', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    $dueSoon = $animal->healthRecords()->create([
        'farm_id' => $user->farm->id,
        'type' => HealthRecordType::Vaccination,
        'description' => 'Due soon',
        'date' => now()->subMonth(),
        'next_due_date' => now()->addDays(10),
    ]);

    $animal->healthRecords()->create([
        'farm_id' => $user->farm->id,
        'type' => HealthRecordType::Vaccination,
        'description' => 'Too far out',
        'date' => now()->subMonth(),
        'next_due_date' => now()->addDays(60),
    ]);

    $records = Livewire::test(Upcoming::class)->instance()->upcomingHealthRecords();

    expect($records->pluck('id')->all())->toBe([$dueSoon->id]);
});

test('upcoming excludes health records belonging to a soft-deleted animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    $animal->healthRecords()->create([
        'farm_id' => $user->farm->id,
        'type' => HealthRecordType::Vaccination,
        'description' => 'Due soon',
        'date' => now()->subMonth(),
        'next_due_date' => now()->addDays(10),
    ]);

    $animal->delete();

    $this->get(route('upcoming.index'))->assertOk();

    $records = Livewire::test(Upcoming::class)->instance()->upcomingHealthRecords();

    expect($records)->toBeEmpty();
});

test('upcoming lists breeding records expected to kid within the next 30 days without an actual kidding date', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    $dueSoon = $doe->breedingRecordsAsDoe()->create([
        'farm_id' => $user->farm->id,
        'mating_date' => now()->subMonths(4),
        'expected_kidding_date' => now()->addDays(15),
    ]);

    $doe->breedingRecordsAsDoe()->create([
        'farm_id' => $user->farm->id,
        'mating_date' => now()->subMonths(5),
        'expected_kidding_date' => now()->addDays(60),
    ]);

    $doe->breedingRecordsAsDoe()->create([
        'farm_id' => $user->farm->id,
        'mating_date' => now()->subMonths(5),
        'expected_kidding_date' => now()->addDays(5),
        'actual_kidding_date' => now()->subDays(1),
    ]);

    $records = Livewire::test(Upcoming::class)->instance()->upcomingBreedingRecords();

    expect($records->pluck('id')->all())->toBe([$dueSoon->id]);
});
