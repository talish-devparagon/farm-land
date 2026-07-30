<?php

use App\Enums\HealthRecordType;
use App\Livewire\HealthRecords\HealthRecordsIndex;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('health records index page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('health-records.index'))->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('health-records.index'))->assertRedirect(route('login'));
});

test('health records index only lists health records for the current farm animals', function () {
    $user = $this->actingAsFarmOwner();
    $ownAnimal = Animal::factory()->for($user->farm)->create();
    $ownHealthRecord = HealthRecord::factory()->for($ownAnimal)->for($user->farm)->create();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    HealthRecord::factory()->for($otherAnimal)->for($otherFarm)->create();

    $healthRecords = Livewire::test(HealthRecordsIndex::class)->instance()->healthRecords();

    expect($healthRecords->pluck('id'))->toContain($ownHealthRecord->id)
        ->and($healthRecords->count())->toBe(1);
});

test('health records index can be searched by animal tag number', function () {
    $user = $this->actingAsFarmOwner();
    $matchingAnimal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-1234']);
    $match = HealthRecord::factory()->for($matchingAnimal)->for($user->farm)->create();

    $otherAnimal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-9999']);
    HealthRecord::factory()->for($otherAnimal)->for($user->farm)->create();

    $healthRecords = Livewire::test(HealthRecordsIndex::class)
        ->set('search', 'TAG-1234')
        ->instance()
        ->healthRecords();

    expect($healthRecords->pluck('id')->all())->toBe([$match->id]);
});

test('health records index can be filtered by type', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $match = HealthRecord::factory()->for($animal)->for($user->farm)->create(['type' => HealthRecordType::Vaccination]);
    HealthRecord::factory()->for($animal)->for($user->farm)->create(['type' => HealthRecordType::Treatment]);

    $healthRecords = Livewire::test(HealthRecordsIndex::class)
        ->set('type', HealthRecordType::Vaccination->value)
        ->instance()
        ->healthRecords();

    expect($healthRecords->pluck('id')->all())->toBe([$match->id]);
});

test('health records index can be filtered by animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $match = HealthRecord::factory()->for($animal)->for($user->farm)->create();

    $otherAnimal = Animal::factory()->for($user->farm)->create();
    HealthRecord::factory()->for($otherAnimal)->for($user->farm)->create();

    $healthRecords = Livewire::test(HealthRecordsIndex::class)
        ->set('animalId', (string) $animal->id)
        ->instance()
        ->healthRecords();

    expect($healthRecords->pluck('id')->all())->toBe([$match->id]);
});

test('health records index can be filtered by date range', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    $inRange = HealthRecord::factory()->for($animal)->for($user->farm)->create(['date' => '2026-06-15']);
    HealthRecord::factory()->for($animal)->for($user->farm)->create(['date' => '2026-01-01']);
    HealthRecord::factory()->for($animal)->for($user->farm)->create(['date' => '2026-12-31']);

    $healthRecords = Livewire::test(HealthRecordsIndex::class)
        ->set('dateFrom', '2026-06-01')
        ->set('dateTo', '2026-06-30')
        ->instance()
        ->healthRecords();

    expect($healthRecords->pluck('id')->all())->toBe([$inRange->id]);
});

test('type options include all health record type enum cases', function () {
    $this->actingAsFarmOwner();

    $types = Livewire::test(HealthRecordsIndex::class)->instance()->types();

    expect($types)->toBe(HealthRecordType::cases());
});

test('animal options only include animals from the current farm', function () {
    $user = $this->actingAsFarmOwner();
    $ownAnimal = Animal::factory()->for($user->farm)->create();

    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create();

    $animalOptions = Livewire::test(HealthRecordsIndex::class)->instance()->animalOptions();

    expect($animalOptions->pluck('id'))->toContain($ownAnimal->id)
        ->and($animalOptions->count())->toBe(1);
});

test('opening the health record modal dispatches the open event with the animal and record ids', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $healthRecord = HealthRecord::factory()->for($animal)->for($user->farm)->create();

    Livewire::test(HealthRecordsIndex::class)
        ->call('openHealthRecordModal', $animal->id, $healthRecord->id)
        ->assertDispatched('open-health-record-modal', animalId: $animal->id, healthRecordId: $healthRecord->id);
});

test('opening the health record modal for a new record dispatches the open event with only the animal id', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(HealthRecordsIndex::class)
        ->call('openHealthRecordModal', $animal->id)
        ->assertDispatched('open-health-record-modal', animalId: $animal->id, healthRecordId: null);
});

test('can delete a health record from the index', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $healthRecord = HealthRecord::factory()->for($animal)->for($user->farm)->create();

    Livewire::test(HealthRecordsIndex::class)
        ->call('deleteHealthRecord', $healthRecord->id);

    expect(HealthRecord::find($healthRecord->id))->toBeNull();
});

test('cannot delete a health record belonging to another farm', function () {
    $this->actingAsFarmOwner();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    $otherHealthRecord = HealthRecord::factory()->for($otherAnimal)->for($otherFarm)->create();

    expect(fn () => Livewire::test(HealthRecordsIndex::class)->call('deleteHealthRecord', $otherHealthRecord->id))
        ->toThrow(ModelNotFoundException::class);
});
