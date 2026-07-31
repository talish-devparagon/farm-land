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

test('months property controls how many months of trend data are returned', function () {
    $this->actingAsFarmOwner();

    $trend = Livewire::test(WeightTrendChart::class, ['months' => 3])->instance()->weightTrend();

    expect($trend)->toHaveCount(3);
});
