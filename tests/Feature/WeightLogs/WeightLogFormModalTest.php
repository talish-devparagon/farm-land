<?php

use App\Livewire\WeightLogs\WeightLogFormModal;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\WeightLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('can create a weight log', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(WeightLogFormModal::class)
        ->call('open', animalId: $animal->id)
        ->assertSet('animalId', $animal->id)
        ->set('weight', '120.5')
        ->set('recorded_at', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('weight-log-saved');

    expect($animal->weightLogs()->count())->toBe(1)
        ->and($animal->fresh()->current_weight)->toEqual('120.50');
});

test('recorded date cannot be in the future', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(WeightLogFormModal::class)
        ->call('open', animalId: $animal->id)
        ->set('weight', '120.5')
        ->set('recorded_at', now()->addDay()->toDateString())
        ->call('save')
        ->assertHasErrors(['recorded_at']);
});

test('can update an existing weight log', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $weightLog = WeightLog::factory()->for($animal)->create(['weight' => 100]);

    Livewire::test(WeightLogFormModal::class)
        ->call('open', weightLogId: $weightLog->id, animalId: $animal->id)
        ->set('weight', '150')
        ->call('save')
        ->assertHasNoErrors();

    expect($weightLog->fresh()->weight)->toEqual('150.00')
        ->and($animal->fresh()->current_weight)->toEqual('150.00');
});

test('can delete a weight log', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $weightLog = WeightLog::factory()->for($animal)->create();

    Livewire::test(WeightLogFormModal::class)
        ->call('open', weightLogId: $weightLog->id, animalId: $animal->id)
        ->call('delete')
        ->assertDispatched('weight-log-saved');

    expect($animal->weightLogs()->count())->toBe(0)
        ->and($animal->fresh()->current_weight)->toBeNull();
});

test('deleting a weight log recalculates the animal current weight to the remaining latest log', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    WeightLog::factory()->for($animal)->create(['weight' => 100, 'recorded_at' => now()->subDays(2)]);
    $latest = WeightLog::factory()->for($animal)->create(['weight' => 200, 'recorded_at' => now()]);

    Livewire::test(WeightLogFormModal::class)
        ->call('open', weightLogId: $latest->id, animalId: $animal->id)
        ->call('delete');

    expect($animal->fresh()->current_weight)->toEqual('100.00');
});

test('can create a weight log for an animal resolved from an animal id, without a bound animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(WeightLogFormModal::class)
        ->call('open', animalId: $animal->id)
        ->set('weight', '120.5')
        ->set('recorded_at', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('weight-log-saved');

    expect($animal->weightLogs()->count())->toBe(1);
});

test('can edit a weight log by resolving the animal from an animal id, without a bound animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $weightLog = WeightLog::factory()->for($animal)->create(['weight' => 100]);

    Livewire::test(WeightLogFormModal::class)
        ->call('open', weightLogId: $weightLog->id, animalId: $animal->id)
        ->set('weight', '150')
        ->call('save')
        ->assertHasNoErrors();

    expect($weightLog->fresh()->weight)->toEqual('150.00');
});

test('cannot resolve an animal from another farm when opening via an animal id', function () {
    $this->actingAsFarmOwner();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();

    expect(fn () => Livewire::test(WeightLogFormModal::class)->call('open', animalId: $otherAnimal->id))
        ->toThrow(ModelNotFoundException::class);
});

test('opening fully unbound with no ids leaves the animal and picker empty', function () {
    $this->actingAsFarmOwner();

    Livewire::test(WeightLogFormModal::class)
        ->call('open')
        ->assertSet('animal', null)
        ->assertSet('animalId', null);
});

test('saving unbound without selecting an animal fails validation', function () {
    $this->actingAsFarmOwner();

    Livewire::test(WeightLogFormModal::class)
        ->call('open')
        ->set('weight', '120.5')
        ->set('recorded_at', now()->toDateString())
        ->call('save')
        ->assertHasErrors('animalId');
});

test('saving unbound after selecting an animal succeeds', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(WeightLogFormModal::class)
        ->call('open')
        ->set('animalId', $animal->id)
        ->set('weight', '120.5')
        ->set('recorded_at', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('weight-log-saved');

    expect($animal->weightLogs()->count())->toBe(1)
        ->and($animal->fresh()->current_weight)->toEqual('120.50');
});

test('opening for a fresh create after editing an existing log does not leave the animal stale', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $weightLog = WeightLog::factory()->for($animal)->create();

    Livewire::test(WeightLogFormModal::class)
        ->call('open', weightLogId: $weightLog->id, animalId: $animal->id)
        ->assertSet('animal.id', $animal->id)
        ->call('open')
        ->assertSet('animal', null)
        ->assertSet('animalId', null);
});
