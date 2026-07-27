<?php

use App\Models\Animal;
use App\Models\WeightLog;
use Carbon\CarbonInterface;

test('weight log casts weight and recorded_at', function () {
    $weightLog = WeightLog::factory()->create();

    expect($weightLog->weight)->toBeString()
        ->and($weightLog->recorded_at)->toBeInstanceOf(CarbonInterface::class);
});

test('weight log belongs to an animal', function () {
    $animal = Animal::factory()->create();
    $weightLog = WeightLog::factory()->for($animal)->create();

    expect($weightLog->animal)->toBeInstanceOf(Animal::class)
        ->and($weightLog->animal->id)->toBe($animal->id);
});
