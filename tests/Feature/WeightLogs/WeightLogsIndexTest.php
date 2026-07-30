<?php

use App\Livewire\WeightLogs\WeightLogsIndex;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\WeightLog;
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

test('herd trend has no data when there are no weight logs', function () {
    $this->actingAsFarmOwner();

    $component = Livewire::test(WeightLogsIndex::class)->instance();

    expect($component->herdTrendHasData())->toBeFalse();
    expect($component->herdTrendPoints())->toBeEmpty();
    expect($component->herdTrendLinePaths())->toBe([]);
    expect($component->herdTrendAreaPaths())->toBe([]);
});

test('herd trend reflects weight logs recorded within the current month', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    WeightLog::factory()->for($animal)->create(['weight' => 100, 'recorded_at' => now()]);
    WeightLog::factory()->for($animal)->create(['weight' => 200, 'recorded_at' => now()]);

    $component = Livewire::test(WeightLogsIndex::class)->instance();

    expect($component->herdTrendHasData())->toBeTrue();

    $currentMonthLabel = now()->format('M');
    $currentMonthPoint = $component->herdTrendPoints()->firstWhere('label', $currentMonthLabel);

    expect($currentMonthPoint['value'])->toBe(150.0);
    expect($component->herdTrendLinePaths())->not->toBe([]);
});
