<?php

use App\Enums\AnimalStatus;
use App\Livewire\Animals\AnimalsIndex;
use App\Models\Animal;
use Livewire\Livewire;

test('animal tag number is rendered in the table', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-4242', 'name' => 'Bessie']);

    Livewire::test(AnimalsIndex::class)
        ->assertSee($animal->tag_number)
        ->assertSee($animal->name);
});

test('status filter lists every animal status option', function () {
    $this->actingAsFarmOwner();

    $component = Livewire::test(AnimalsIndex::class);

    foreach (AnimalStatus::cases() as $status) {
        $component->assertSee(ucfirst($status->value));
    }
});

test('empty state is shown when no animals match', function () {
    $this->actingAsFarmOwner();

    Livewire::test(AnimalsIndex::class)
        ->set('search', 'no-such-animal')
        ->assertSee(__('No animals found'));
});
