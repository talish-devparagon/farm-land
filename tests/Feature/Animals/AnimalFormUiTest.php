<?php

use App\Livewire\Animals\AnimalForm;
use App\Models\Animal;
use Livewire\Livewire;

test('create form shows the add animal heading', function () {
    $this->actingAsFarmOwner();

    Livewire::test(AnimalForm::class)
        ->assertSee(__('Add animal'));
});

test('edit form shows the edit animal heading and loads the existing tag number', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-7777']);

    Livewire::test(AnimalForm::class, ['animal' => $animal])
        ->assertSee(__('Edit animal'))
        ->assertSet('tag_number', 'TAG-7777');
});

test('candidate parents appear as options when editing', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $candidate = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-PARENT']);

    Livewire::test(AnimalForm::class, ['animal' => $animal])
        ->assertSee($candidate->tag_number);
});
