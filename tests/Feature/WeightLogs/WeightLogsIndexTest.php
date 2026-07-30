<?php

use App\Livewire\WeightLogs\WeightLogsIndex;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\WeightLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

test('weight logs index page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('weight-logs.index'))->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('weight-logs.index'))->assertRedirect(route('login'));
});

test('weight logs index only lists weight logs for the current farm animals', function () {
    $user = $this->actingAsFarmOwner();
    $ownAnimal = Animal::factory()->for($user->farm)->create();
    $ownWeightLog = WeightLog::factory()->for($ownAnimal)->create();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    WeightLog::factory()->for($otherAnimal)->create();

    $weightLogs = Livewire::test(WeightLogsIndex::class)->instance()->weightLogs();

    expect($weightLogs->pluck('id'))->toContain($ownWeightLog->id)
        ->and($weightLogs->count())->toBe(1);
});

test('weight logs index can be searched by animal tag number', function () {
    $user = $this->actingAsFarmOwner();
    $matchingAnimal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-1234']);
    $match = WeightLog::factory()->for($matchingAnimal)->create();

    $otherAnimal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-9999']);
    WeightLog::factory()->for($otherAnimal)->create();

    $weightLogs = Livewire::test(WeightLogsIndex::class)
        ->set('search', 'TAG-1234')
        ->instance()
        ->weightLogs();

    expect($weightLogs->pluck('id')->all())->toBe([$match->id]);
});

test('weight logs index can be filtered by animal', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $match = WeightLog::factory()->for($animal)->create();

    $otherAnimal = Animal::factory()->for($user->farm)->create();
    WeightLog::factory()->for($otherAnimal)->create();

    $weightLogs = Livewire::test(WeightLogsIndex::class)
        ->set('animalId', (string) $animal->id)
        ->instance()
        ->weightLogs();

    expect($weightLogs->pluck('id')->all())->toBe([$match->id]);
});

test('weight logs index can be filtered by date range', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    $inRange = WeightLog::factory()->for($animal)->create(['recorded_at' => '2026-06-15']);
    WeightLog::factory()->for($animal)->create(['recorded_at' => '2026-01-01']);
    WeightLog::factory()->for($animal)->create(['recorded_at' => '2026-12-31']);

    $weightLogs = Livewire::test(WeightLogsIndex::class)
        ->set('dateFrom', '2026-06-01')
        ->set('dateTo', '2026-06-30')
        ->instance()
        ->weightLogs();

    expect($weightLogs->pluck('id')->all())->toBe([$inRange->id]);
});

test('animal options only include animals from the current farm', function () {
    $user = $this->actingAsFarmOwner();
    $ownAnimal = Animal::factory()->for($user->farm)->create();

    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create();

    $animalOptions = Livewire::test(WeightLogsIndex::class)->instance()->animalOptions();

    expect($animalOptions->pluck('id'))->toContain($ownAnimal->id)
        ->and($animalOptions->count())->toBe(1);
});

test('opening the weight log modal dispatches the open event with the animal and weight log ids', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $weightLog = WeightLog::factory()->for($animal)->create();

    Livewire::test(WeightLogsIndex::class)
        ->call('openWeightLogModal', $animal->id, $weightLog->id)
        ->assertDispatched('open-weight-log-modal', animalId: $animal->id, weightLogId: $weightLog->id);
});

test('opening the weight log modal for a new log dispatches the open event with only the animal id', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(WeightLogsIndex::class)
        ->call('openWeightLogModal', $animal->id)
        ->assertDispatched('open-weight-log-modal', animalId: $animal->id, weightLogId: null);
});

test('can delete a weight log from the index and it recalculates the animal current weight', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    WeightLog::factory()->for($animal)->create(['weight' => 100, 'recorded_at' => now()->subDays(2)]);
    $latest = WeightLog::factory()->for($animal)->create(['weight' => 200, 'recorded_at' => now()]);

    Livewire::test(WeightLogsIndex::class)
        ->call('deleteWeightLog', $latest->id);

    expect(WeightLog::find($latest->id))->toBeNull()
        ->and($animal->fresh()->current_weight)->toEqual('100.00');
});

test('cannot delete a weight log belonging to another farm', function () {
    $this->actingAsFarmOwner();

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    $otherWeightLog = WeightLog::factory()->for($otherAnimal)->create();

    expect(fn () => Livewire::test(WeightLogsIndex::class)->call('deleteWeightLog', $otherWeightLog->id))
        ->toThrow(ModelNotFoundException::class);
});
