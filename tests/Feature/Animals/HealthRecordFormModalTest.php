<?php

use App\Enums\HealthRecordType;
use App\Livewire\Animals\HealthRecordFormModal;
use App\Models\Animal;
use App\Models\Farm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('can create a health record', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(HealthRecordFormModal::class, ['animal' => $animal])
        ->call('open')
        ->set('type', HealthRecordType::Vaccination->value)
        ->set('description', 'Annual shots')
        ->set('date', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('health-record-saved');

    expect($animal->healthRecords()->count())->toBe(1);
});

test('next due date must be after the record date', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(HealthRecordFormModal::class, ['animal' => $animal])
        ->call('open')
        ->set('type', HealthRecordType::Treatment->value)
        ->set('description', 'Wound care')
        ->set('date', now()->toDateString())
        ->set('next_due_date', now()->subDay()->toDateString())
        ->call('save')
        ->assertHasErrors(['next_due_date']);
});

test('can update an existing health record', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $healthRecord = $animal->healthRecords()->create([
        'farm_id' => $user->farm->id,
        'type' => HealthRecordType::Vaccination,
        'description' => 'Original',
        'date' => now(),
    ]);

    Livewire::test(HealthRecordFormModal::class, ['animal' => $animal])
        ->call('open', $healthRecord->id)
        ->set('description', 'Updated description')
        ->call('save')
        ->assertHasNoErrors();

    expect($healthRecord->fresh()->description)->toBe('Updated description');
});

test('can delete a health record', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $healthRecord = $animal->healthRecords()->create([
        'farm_id' => $user->farm->id,
        'type' => HealthRecordType::Vaccination,
        'description' => 'To delete',
        'date' => now(),
    ]);

    Livewire::test(HealthRecordFormModal::class, ['animal' => $animal])
        ->call('open', $healthRecord->id)
        ->call('delete')
        ->assertDispatched('health-record-saved');

    expect($animal->healthRecords()->count())->toBe(0);
});

test('can create a health record for an animal resolved from an animal id, without a bound animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(HealthRecordFormModal::class)
        ->call('open', animalId: $animal->id)
        ->set('type', HealthRecordType::Vaccination->value)
        ->set('description', 'Annual shots')
        ->set('date', now()->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('health-record-saved');

    expect($animal->healthRecords()->count())->toBe(1);
});

test('can edit a health record by resolving the animal from an animal id, without a bound animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $healthRecord = $animal->healthRecords()->create([
        'farm_id' => $user->farm->id,
        'type' => HealthRecordType::Vaccination,
        'description' => 'Original',
        'date' => now(),
    ]);

    Livewire::test(HealthRecordFormModal::class)
        ->call('open', healthRecordId: $healthRecord->id, animalId: $animal->id)
        ->set('description', 'Updated description')
        ->call('save')
        ->assertHasNoErrors();

    expect($healthRecord->fresh()->description)->toBe('Updated description');
});

test('cannot resolve an animal from another farm when opening via an animal id', function () {
    $this->actingAsFarmOwner();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();

    expect(fn () => Livewire::test(HealthRecordFormModal::class)->call('open', animalId: $otherAnimal->id))
        ->toThrow(ModelNotFoundException::class);
});
