<?php

use App\Enums\AnimalStatus;
use App\Livewire\Animals\AnimalsIndex;
use App\Models\Animal;
use App\Models\Farm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('animals index page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('animals.index'))->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('animals.index'))->assertRedirect(route('login'));
});

test('animals index only lists the current farm animals', function () {
    $user = $this->actingAsFarmOwner();
    $ownAnimal = Animal::factory()->for($user->farm)->create();

    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create();

    $animals = Livewire::test(AnimalsIndex::class)->instance()->animals();

    expect($animals->pluck('id'))->toContain($ownAnimal->id)
        ->and($animals->count())->toBe(1);
});

test('animals index can be searched by tag number', function () {
    $user = $this->actingAsFarmOwner();
    $match = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-1234']);
    Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-9999']);

    $animals = Livewire::test(AnimalsIndex::class)
        ->set('search', 'TAG-1234')
        ->instance()
        ->animals();

    expect($animals->pluck('id')->all())->toBe([$match->id]);
});

test('animals index can be filtered by status', function () {
    $user = $this->actingAsFarmOwner();
    $alive = Animal::factory()->for($user->farm)->create(['status' => AnimalStatus::Alive]);
    Animal::factory()->for($user->farm)->create(['status' => AnimalStatus::Sold]);

    $animals = Livewire::test(AnimalsIndex::class)
        ->set('status', AnimalStatus::Alive->value)
        ->instance()
        ->animals();

    expect($animals->pluck('id')->all())->toBe([$alive->id]);
});

test('can delete an animal from the index', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(AnimalsIndex::class)
        ->call('delete', $animal->id);

    expect(Animal::find($animal->id))->toBeNull();
});

test('cannot delete an animal belonging to another farm', function () {
    $this->actingAsFarmOwner();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();

    expect(fn () => Livewire::test(AnimalsIndex::class)->call('delete', $otherAnimal->id))
        ->toThrow(ModelNotFoundException::class);
});
