<?php

use App\Livewire\Animals\AnimalShow;
use App\Models\Animal;
use App\Models\Farm;
use Livewire\Livewire;

test('animal show page is displayed', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    $this->get(route('animals.show', $animal))->assertOk();
});

test('cannot view another farms animal', function () {
    $this->actingAsFarmOwner();
    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();

    $this->get(route('animals.show', $otherAnimal))->assertNotFound();
});

test('logging a weight recalculates current weight using the latest recorded_at, not insertion order', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['current_weight' => null]);

    $animal->weightLogs()->create([
        'weight' => 150,
        'recorded_at' => now()->subDays(2),
    ]);

    Livewire::test(AnimalShow::class, ['animal' => $animal])
        ->set('weight', '90')
        ->set('recorded_at', now()->subDays(10)->toDateString())
        ->call('logWeight')
        ->assertHasNoErrors();

    expect($animal->fresh()->current_weight)->toBe('150.00');
});

test('delete removes the animal and redirects to the index', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(AnimalShow::class, ['animal' => $animal])
        ->call('delete')
        ->assertRedirect(route('animals.index'));

    expect($animal->fresh()->trashed())->toBeTrue()
        ->and(Animal::find($animal->id))->toBeNull();
});

test('opening the health record modal dispatches an event with the record id', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(AnimalShow::class, ['animal' => $animal])
        ->call('openHealthRecordModal', 42)
        ->assertDispatched('open-health-record-modal', healthRecordId: 42);
});

test('opening the breeding record modal dispatches an event with the record id', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(AnimalShow::class, ['animal' => $animal])
        ->call('openBreedingRecordModal', 7)
        ->assertDispatched('open-breeding-record-modal', breedingRecordId: 7);
});
