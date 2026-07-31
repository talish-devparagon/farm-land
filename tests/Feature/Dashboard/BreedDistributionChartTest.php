<?php

use App\Enums\AnimalStatus;
use App\Livewire\Dashboard\BreedDistributionChart;
use App\Models\Animal;
use Livewire\Livewire;

test('breed distribution counts only alive animals grouped by breed, sorted descending', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->count(3)->create(['breed' => 'Holstein', 'status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->count(2)->create(['breed' => 'Jersey', 'status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->create(['breed' => 'Angus', 'status' => AnimalStatus::Sold]);

    $distribution = Livewire::test(BreedDistributionChart::class)->instance()->breedDistribution();

    expect($distribution)->toBe(['Holstein' => 3, 'Jersey' => 2]);
});

test('total counts only alive animals', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->count(2)->create(['status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->create(['status' => AnimalStatus::Sold]);

    $total = Livewire::test(BreedDistributionChart::class)->instance()->total();

    expect($total)->toBe(2);
});

test('segments cap at the top 4 breeds and bucket the rest into other', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->count(5)->create(['breed' => 'Holstein', 'status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->count(4)->create(['breed' => 'Jersey', 'status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->count(3)->create(['breed' => 'Angus', 'status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->count(2)->create(['breed' => 'Hereford', 'status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->count(1)->create(['breed' => 'Nubian', 'status' => AnimalStatus::Alive]);

    $segments = Livewire::test(BreedDistributionChart::class)->instance()->segments();

    expect($segments)->toHaveCount(5);
    expect($segments->pluck('label')->all())->toBe(['Holstein', 'Jersey', 'Angus', 'Hereford', 'Other']);
    expect($segments->firstWhere('label', 'Other'))->toMatchArray(['count' => 1, 'color' => 'zinc']);

    foreach ($segments as $segment) {
        expect($segment)->toHaveKeys(['label', 'count', 'percent', 'color']);
    }

    expect((int) round($segments->sum('percent')))->toBe(100);
});

test('segments are empty when there are no alive animals', function () {
    $this->actingAsFarmOwner();

    $segments = Livewire::test(BreedDistributionChart::class)->instance()->segments();

    expect($segments)->toBeEmpty();
});
