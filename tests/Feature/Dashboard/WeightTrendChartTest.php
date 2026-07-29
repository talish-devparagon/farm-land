<?php

use App\Livewire\Dashboard\WeightTrendChart;
use App\Models\Animal;
use App\Models\WeightLog;
use Livewire\Livewire;

test('weight trend averages weight logs recorded within each month', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    WeightLog::factory()->for($animal)->create(['weight' => 100, 'recorded_at' => now()]);
    WeightLog::factory()->for($animal)->create(['weight' => 200, 'recorded_at' => now()]);

    $trend = Livewire::test(WeightTrendChart::class)->instance()->weightTrend();
    $currentMonthLabel = now()->format('M');

    expect($trend[$currentMonthLabel])->toBe(150.0);
});

test('weight trend falls back to the herd average current weight when no logs are recorded', function () {
    $user = $this->actingAsFarmOwner();
    Animal::factory()->for($user->farm)->create(['current_weight' => 100]);
    Animal::factory()->for($user->farm)->create(['current_weight' => 200]);

    $trend = Livewire::test(WeightTrendChart::class)->instance()->weightTrend();

    expect($trend)->toHaveCount(6);
    expect(array_values($trend))->toBe(array_fill(0, 6, 150.0));
});

test('has data is false when there are no weight logs or animals with a current weight', function () {
    $this->actingAsFarmOwner();

    $component = Livewire::test(WeightTrendChart::class)->instance();

    expect($component->hasData())->toBeFalse();
    expect($component->points())->toBeEmpty();
    expect($component->linePaths())->toBe([]);
    expect($component->areaPaths())->toBe([]);
});

test('weight trend points and paths split into separate segments across a gap in the data', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    WeightLog::factory()->for($animal)->create(['weight' => 100, 'recorded_at' => now()->startOfMonth()->subMonths(5)]);
    WeightLog::factory()->for($animal)->create(['weight' => 300, 'recorded_at' => now()]);

    $component = Livewire::test(WeightTrendChart::class)->instance();

    expect($component->hasData())->toBeTrue();

    $points = $component->points();
    expect($points)->toHaveCount(6);

    foreach ($points as $point) {
        expect($point)->toHaveKeys(['label', 'value', 'x', 'y']);
    }

    // Months 2-5 (index 1 through 4) have no recorded weight, so their y is null.
    for ($index = 1; $index <= 4; $index++) {
        expect($points->values()[$index]['y'])->toBeNull();
    }

    $linePaths = $component->linePaths();
    $areaPaths = $component->areaPaths();

    expect($linePaths)->toHaveCount(2);
    expect($areaPaths)->toHaveCount(2);

    foreach ($linePaths as $path) {
        expect($path)->toStartWith('M');
    }

    foreach ($areaPaths as $path) {
        expect($path)->toStartWith('M')->toEndWith('Z');
    }
});
