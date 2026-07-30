<?php

use App\Actions\Reports\GetGrowthSummaryAction;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\User;
use App\Models\WeightLog;

test('it averages weight logs recorded within each month of the selected range', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();
    WeightLog::factory()->for($animal)->create(['weight' => 100, 'recorded_at' => now()]);
    WeightLog::factory()->for($animal)->create(['weight' => 200, 'recorded_at' => now()]);

    $result = (new GetGrowthSummaryAction)->handle(6);
    $currentMonthLabel = now()->format('M');

    expect($result['monthlyAverageWeight'])->toHaveCount(6)
        ->and($result['monthlyAverageWeight'][$currentMonthLabel])->toBe(150.0)
        ->and($result['overallAverageWeight'])->toBe(150.0)
        ->and($result['totalWeightLogs'])->toBe(2);
});

test('a wider range includes weight logs that a narrower range excludes', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();
    WeightLog::factory()->for($animal)->create(['weight' => 300, 'recorded_at' => now()->subMonths(8)]);

    expect((new GetGrowthSummaryAction)->handle(6)['totalWeightLogs'])->toBe(0)
        ->and((new GetGrowthSummaryAction)->handle(12)['totalWeightLogs'])->toBe(1);
});

test('summary is empty but well-formed when there are no weight logs', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $result = (new GetGrowthSummaryAction)->handle(3);

    expect($result['totalWeightLogs'])->toBe(0)
        ->and($result['overallAverageWeight'])->toBeNull()
        ->and(collect($result['monthlyAverageWeight'])->every(fn (?float $value) => $value === null))->toBeTrue();
});

test('it does not include weight logs for another farm animal', function () {
    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    WeightLog::factory()->for($otherAnimal)->create(['recorded_at' => now()]);

    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $result = (new GetGrowthSummaryAction)->handle(6);

    expect($result['totalWeightLogs'])->toBe(0);
});
