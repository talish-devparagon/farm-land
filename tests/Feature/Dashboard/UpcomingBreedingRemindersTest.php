<?php

use App\Enums\AnimalSex;
use App\Livewire\Dashboard\UpcomingBreedingReminders;
use App\Models\Animal;
use App\Models\BreedingRecord;
use Livewire\Livewire;

test('a due-soon breeding record shows its doe tag number', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-2002', 'sex' => AnimalSex::Female]);
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(10),
        'actual_kidding_date' => null,
    ]);

    Livewire::test(UpcomingBreedingReminders::class)
        ->assertSee('TAG-2002');
});

test('empty state copy is shown when nothing is due', function () {
    $this->actingAsFarmOwner();

    Livewire::test(UpcomingBreedingReminders::class)
        ->assertSee('Nothing due in the next 30 days.');
});

test('records computed property excludes breeding records that have already kidded', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(10),
        'actual_kidding_date' => now(),
    ]);

    $records = Livewire::test(UpcomingBreedingReminders::class)->instance()->records();

    expect($records)->toHaveCount(0);
});
