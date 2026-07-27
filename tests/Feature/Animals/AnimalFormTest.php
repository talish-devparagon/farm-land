<?php

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Livewire\Animals\AnimalForm;
use App\Models\Animal;
use App\Models\Farm;
use Livewire\Livewire;

test('can create an animal', function () {
    $user = $this->actingAsFarmOwner();

    Livewire::test(AnimalForm::class)
        ->set('tag_number', 'TAG-0001')
        ->set('name', 'Bessie')
        ->set('sex', AnimalSex::Female->value)
        ->set('status', AnimalStatus::Alive->value)
        ->set('current_weight', '120.50')
        ->call('save')
        ->assertHasNoErrors();

    $animal = Animal::where('tag_number', 'TAG-0001')->firstOrFail();

    expect($animal->farm_id)->toBe($user->farm->id)
        ->and($animal->name)->toBe('Bessie')
        ->and($animal->sex)->toBe(AnimalSex::Female);
});

test('tag number must be unique within the current farm', function () {
    $user = $this->actingAsFarmOwner();
    Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-DUP']);

    Livewire::test(AnimalForm::class)
        ->set('tag_number', 'TAG-DUP')
        ->set('sex', AnimalSex::Female->value)
        ->set('status', AnimalStatus::Alive->value)
        ->call('save')
        ->assertHasErrors(['tag_number']);
});

test('tag number can be reused across different farms', function () {
    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create(['tag_number' => 'TAG-SHARED']);

    $this->actingAsFarmOwner();

    Livewire::test(AnimalForm::class)
        ->set('tag_number', 'TAG-SHARED')
        ->set('sex', AnimalSex::Female->value)
        ->set('status', AnimalStatus::Alive->value)
        ->call('save')
        ->assertHasNoErrors();
});

test('can update an existing animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['name' => 'Old Name']);

    Livewire::test(AnimalForm::class, ['animal' => $animal])
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    expect($animal->fresh()->name)->toBe('New Name');
});

test('cannot edit another farms animal', function () {
    $this->actingAsFarmOwner();
    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();

    $this->get(route('animals.edit', $otherAnimal))->assertNotFound();
});

test('a mother cannot be set to the animal itself', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(AnimalForm::class, ['animal' => $animal])
        ->set('mother_id', $animal->id)
        ->call('save')
        ->assertHasErrors(['mother_id']);
});

test('candidate parents excludes the animal itself when editing', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $other = Animal::factory()->for($user->farm)->create();

    $candidates = Livewire::test(AnimalForm::class, ['animal' => $animal])
        ->instance()
        ->candidateParents();

    expect($candidates->pluck('id'))->toContain($other->id)
        ->not->toContain($animal->id);
});
