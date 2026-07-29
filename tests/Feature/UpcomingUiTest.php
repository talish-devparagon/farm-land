<?php

use App\Enums\AnimalSex;
use App\Livewire\Upcoming;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\HealthRecord;
use Livewire\Livewire;

test('a due-soon health record shows its animal tag number', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-1001']);
    HealthRecord::factory()->for($animal)->for($user->farm)->dueSoon()->create();

    Livewire::test(Upcoming::class)
        ->assertSee('TAG-1001');
});

test('a due-soon breeding record shows its doe tag number', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-2002', 'sex' => AnimalSex::Female]);
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create([
        'expected_kidding_date' => now()->addDays(10),
        'actual_kidding_date' => null,
    ]);

    Livewire::test(Upcoming::class)
        ->assertSee('TAG-2002');
});

test('empty state copy is shown when nothing is due', function () {
    $this->actingAsFarmOwner();

    Livewire::test(Upcoming::class)
        ->assertSee('Nothing due in the next 30 days.');
});
