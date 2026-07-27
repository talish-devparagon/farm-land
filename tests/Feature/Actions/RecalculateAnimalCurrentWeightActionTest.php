<?php

use App\Actions\RecalculateAnimalCurrentWeightAction;
use App\Models\Animal;
use App\Models\WeightLog;

test('it sets the animal current weight to the reading with the latest recorded_at, not the most recently inserted', function () {
    $animal = Animal::factory()->create(['current_weight' => 100]);

    $mostRecentLog = WeightLog::factory()->for($animal)->create([
        'weight' => 200,
        'recorded_at' => now()->subDays(10),
    ]);

    // Inserted after the log above, but for an earlier date — must not win.
    WeightLog::factory()->for($animal)->create([
        'weight' => 120,
        'recorded_at' => now()->subDays(20),
    ]);

    (new RecalculateAnimalCurrentWeightAction)->handle($animal);

    expect($animal->fresh()->current_weight)->toBe($mostRecentLog->fresh()->weight);
});

test('it sets current weight to null when the animal has no weight logs', function () {
    $animal = Animal::factory()->create(['current_weight' => 100]);

    (new RecalculateAnimalCurrentWeightAction)->handle($animal);

    expect($animal->fresh()->current_weight)->toBeNull();
});
