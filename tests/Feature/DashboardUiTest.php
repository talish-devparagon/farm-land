<?php

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Livewire\Dashboard;
use App\Models\Animal;
use Livewire\Livewire;

test('dashboard page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('dashboard'))->assertOk();
});

test('herd overview counts animals by status and sex, with a male/female percent split', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->count(2)->create(['status' => AnimalStatus::Alive, 'sex' => AnimalSex::Female]);
    Animal::factory()->for($user->farm)->create(['status' => AnimalStatus::Alive, 'sex' => AnimalSex::Male]);
    Animal::factory()->for($user->farm)->create(['status' => AnimalStatus::Sold, 'sex' => AnimalSex::Male]);
    Animal::factory()->for($user->farm)->create(['status' => AnimalStatus::Deceased, 'sex' => AnimalSex::Female]);

    $overview = Livewire::test(Dashboard::class)->instance()->herdOverview();

    expect($overview)->toBe([
        'total' => 5,
        'alive' => 3,
        'sold' => 1,
        'deceased' => 1,
        'male' => 2,
        'female' => 3,
        'malePercent' => 40,
        'femalePercent' => 60,
    ]);
});

test('herd overview excludes soft-deleted animals', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->create()->delete();

    $overview = Livewire::test(Dashboard::class)->instance()->herdOverview();

    expect($overview['total'])->toBe(0);
});
