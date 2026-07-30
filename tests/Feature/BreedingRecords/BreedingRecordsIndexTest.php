<?php

use App\Enums\AnimalSex;
use App\Livewire\BreedingRecords\BreedingRecordsIndex;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\Farm;
use Livewire\Livewire;

test('breeding records index page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('breeding-records.index'))->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('breeding-records.index'))->assertRedirect(route('login'));
});

test('breeding records index only lists the current farm records', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $ownRecord = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create();

    $otherFarm = Farm::factory()->create();
    $otherDoe = Animal::factory()->for($otherFarm)->create(['sex' => AnimalSex::Female]);
    BreedingRecord::factory()->for($otherFarm)->for($otherDoe, 'doe')->create();

    $records = Livewire::test(BreedingRecordsIndex::class)->instance()->breedingRecords();

    expect($records->pluck('id'))->toContain($ownRecord->id)
        ->and($records->count())->toBe(1);
});

test('breeding records index can be searched by doe tag number', function () {
    $user = $this->actingAsFarmOwner();
    $matchingDoe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female, 'tag_number' => 'DOE-1234']);
    $otherDoe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female, 'tag_number' => 'DOE-9999']);

    $match = BreedingRecord::factory()->for($user->farm)->for($matchingDoe, 'doe')->create();
    BreedingRecord::factory()->for($user->farm)->for($otherDoe, 'doe')->create();

    $records = Livewire::test(BreedingRecordsIndex::class)
        ->set('search', 'DOE-1234')
        ->instance()
        ->breedingRecords();

    expect($records->pluck('id')->all())->toBe([$match->id]);
});

test('breeding records index can be searched by buck tag number', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $matchingBuck = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male, 'tag_number' => 'BUCK-1234']);
    $otherBuck = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male, 'tag_number' => 'BUCK-9999']);

    $match = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->for($matchingBuck, 'buck')->create();
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->for($otherBuck, 'buck')->create();

    $records = Livewire::test(BreedingRecordsIndex::class)
        ->set('search', 'BUCK-1234')
        ->instance()
        ->breedingRecords();

    expect($records->pluck('id')->all())->toBe([$match->id]);
});

test('breeding records index can be filtered by pending status', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    $pending = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create(['actual_kidding_date' => null]);
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->completed()->create();

    $records = Livewire::test(BreedingRecordsIndex::class)
        ->set('status', 'pending')
        ->instance()
        ->breedingRecords();

    expect($records->pluck('id')->all())->toBe([$pending->id]);
});

test('breeding records index can be filtered by complete status', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create(['actual_kidding_date' => null]);
    $completed = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->completed()->create();

    $records = Livewire::test(BreedingRecordsIndex::class)
        ->set('status', 'complete')
        ->instance()
        ->breedingRecords();

    expect($records->pluck('id')->all())->toBe([$completed->id]);
});

test('breeding records index can be filtered by doe', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $otherDoe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    $match = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create();
    BreedingRecord::factory()->for($user->farm)->for($otherDoe, 'doe')->create();

    $records = Livewire::test(BreedingRecordsIndex::class)
        ->set('doeId', (string) $doe->id)
        ->instance()
        ->breedingRecords();

    expect($records->pluck('id')->all())->toBe([$match->id]);
});

test('breeding records index can be filtered by mating date range', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    $inRange = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create(['mating_date' => now()->subMonths(2)]);
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create(['mating_date' => now()->subMonths(6)]);

    $records = Livewire::test(BreedingRecordsIndex::class)
        ->set('dateFrom', now()->subMonths(3)->toDateString())
        ->set('dateTo', now()->subMonth()->toDateString())
        ->instance()
        ->breedingRecords();

    expect($records->pluck('id')->all())->toBe([$inRange->id]);
});

test('doe options computed property only lists female animals for the current farm', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male]);

    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create(['sex' => AnimalSex::Female]);

    $doeOptions = Livewire::test(BreedingRecordsIndex::class)->instance()->doeOptions();

    expect($doeOptions->pluck('id')->all())->toBe([$doe->id]);
});

test('statuses computed property returns pending and complete options', function () {
    $this->actingAsFarmOwner();

    $statuses = Livewire::test(BreedingRecordsIndex::class)->instance()->statuses();

    expect($statuses)->toBe([
        'pending' => 'Pending',
        'complete' => 'Complete',
    ]);
});

test('opening the breeding record modal dispatches the open event with the doe and record ids', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $breedingRecord = BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create();

    Livewire::test(BreedingRecordsIndex::class)
        ->call('openBreedingRecordModal', $doe->id, $breedingRecord->id)
        ->assertDispatched('open-breeding-record-modal', doeId: $doe->id, breedingRecordId: $breedingRecord->id);
});

test('opening the breeding record modal for a new record dispatches the open event with only the doe id', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    Livewire::test(BreedingRecordsIndex::class)
        ->call('openBreedingRecordModal', $doe->id)
        ->assertDispatched('open-breeding-record-modal', doeId: $doe->id, breedingRecordId: null);
});
