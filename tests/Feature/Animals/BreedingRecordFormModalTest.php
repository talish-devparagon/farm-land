<?php

use App\Enums\AnimalSex;
use App\Livewire\Animals\BreedingRecordFormModal;
use App\Models\Animal;
use App\Models\Farm;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('can create a breeding record', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $buck = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male]);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->set('buck_id', $buck->id)
        ->set('mating_date', now()->subMonth()->toDateString())
        ->set('expected_kidding_date', now()->addMonths(4)->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('breeding-record-saved');

    expect($doe->breedingRecordsAsDoe()->count())->toBe(1);
});

test('buck must belong to the same farm and be male', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $otherFemale = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->set('buck_id', $otherFemale->id)
        ->set('mating_date', now()->subMonth()->toDateString())
        ->set('expected_kidding_date', now()->addMonths(4)->toDateString())
        ->call('save')
        ->assertHasErrors(['buck_id']);
});

test('recording an actual kidding date with offspring creates the offspring animals', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $buck = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male]);

    $matingDate = now()->subMonths(5);
    $kiddingDate = now();

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->set('buck_id', $buck->id)
        ->set('mating_date', $matingDate->toDateString())
        ->set('expected_kidding_date', $matingDate->copy()->addDays(150)->toDateString())
        ->set('actual_kidding_date', $kiddingDate->toDateString())
        ->set('create_offspring', true)
        ->set('offspring', [
            ['tag_number' => 'KID-001', 'name' => 'Kid One', 'sex' => AnimalSex::Female->value],
            ['tag_number' => 'KID-002', 'name' => 'Kid Two', 'sex' => AnimalSex::Male->value],
        ])
        ->call('save')
        ->assertHasNoErrors();

    $breedingRecord = $doe->breedingRecordsAsDoe()->firstOrFail();

    expect($breedingRecord->number_of_offspring)->toBe(2);

    $offspring = Animal::whereIn('tag_number', ['KID-001', 'KID-002'])->get();

    expect($offspring)->toHaveCount(2)
        ->and($offspring->pluck('mother_id')->unique()->all())->toBe([$doe->id])
        ->and($offspring->pluck('father_id')->unique()->all())->toBe([$buck->id])
        ->and($offspring->pluck('farm_id')->unique()->all())->toBe([$user->farm->id]);
});

test('offspring tag numbers must be unique within the farm', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    Animal::factory()->for($user->farm)->create(['tag_number' => 'KID-001']);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open')
        ->set('mating_date', now()->subMonths(5)->toDateString())
        ->set('expected_kidding_date', now()->subMonths(5)->addDays(150)->toDateString())
        ->set('actual_kidding_date', now()->toDateString())
        ->set('create_offspring', true)
        ->set('offspring', [
            ['tag_number' => 'KID-001', 'name' => null, 'sex' => AnimalSex::Female->value],
        ])
        ->call('save')
        ->assertHasErrors(['offspring.0.tag_number']);
});

test('can create a breeding record by resolving the doe from an id when no animal is bound', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $buck = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Male]);

    Livewire::test(BreedingRecordFormModal::class)
        ->call('open', doeId: $doe->id)
        ->set('buck_id', $buck->id)
        ->set('mating_date', now()->subMonth()->toDateString())
        ->set('expected_kidding_date', now()->addMonths(4)->toDateString())
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('breeding-record-saved');

    expect($doe->breedingRecordsAsDoe()->count())->toBe(1);
});

test('can edit an existing breeding record by resolving the doe from an id when no animal is bound', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $breedingRecord = $doe->breedingRecordsAsDoe()->create([
        'farm_id' => $user->farm->id,
        'mating_date' => now()->subMonth(),
        'expected_kidding_date' => now()->addMonths(4),
    ]);

    Livewire::test(BreedingRecordFormModal::class)
        ->call('open', breedingRecordId: $breedingRecord->id, doeId: $doe->id)
        ->assertSet('breedingRecordId', $breedingRecord->id)
        ->set('notes', 'Updated notes')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('breeding-record-saved');

    expect($doe->breedingRecordsAsDoe()->firstOrFail()->notes)->toBe('Updated notes');
});

test('cannot resolve a doe belonging to another farm via doeId', function () {
    $this->actingAsFarmOwner();
    $otherFarm = Farm::factory()->create();
    $otherDoe = Animal::factory()->for($otherFarm)->create(['sex' => AnimalSex::Female]);

    expect(fn () => Livewire::test(BreedingRecordFormModal::class)->call('open', doeId: $otherDoe->id))
        ->toThrow(ModelNotFoundException::class);
});

test('can delete a breeding record', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);
    $breedingRecord = $doe->breedingRecordsAsDoe()->create([
        'farm_id' => $user->farm->id,
        'mating_date' => now()->subMonth(),
        'expected_kidding_date' => now()->addMonths(4),
    ]);

    Livewire::test(BreedingRecordFormModal::class, ['animal' => $doe])
        ->call('open', $breedingRecord->id)
        ->call('delete')
        ->assertDispatched('breeding-record-saved');

    expect($doe->breedingRecordsAsDoe()->count())->toBe(0);
});
