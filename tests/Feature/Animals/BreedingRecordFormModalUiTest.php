<?php

use App\Enums\AnimalSex;
use App\Livewire\Animals\BreedingRecordFormModal;
use App\Models\Animal;
use Livewire\Livewire;

test('renders the available bucks in the select', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $buck = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male, 'tag_number' => 'BUCK-9']);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->assertSee('BUCK-9');
});

test('shows the offspring repeater once an actual kidding date and offspring tracking are set', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->assertDontSee('Add offspring')
        ->set('actual_kidding_date', now()->toDateString())
        ->set('create_offspring', true)
        ->call('addOffspring')
        ->assertSee('Add offspring')
        ->assertSee('Tag number');
});

test('shows a delete button only when editing an existing record', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $breedingRecord = $doe->breedingRecordsAsDoe()->create([
        'mating_date' => now()->subMonth(),
        'expected_kidding_date' => now()->addMonths(4),
    ]);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->assertDontSee('Delete')
        ->call('open', $breedingRecord->id)
        ->assertSee('Delete');
});
