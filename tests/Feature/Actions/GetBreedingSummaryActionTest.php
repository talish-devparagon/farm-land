<?php

use App\Actions\Reports\GetBreedingSummaryAction;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\Farm;
use App\Models\User;

test('it summarizes matings, success rate, and average offspring within the selected range', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $doe = Animal::factory()->for($farm)->create();

    BreedingRecord::factory()->for($farm)->for($doe, 'doe')->completed()->create([
        'mating_date' => now()->subMonths(2),
        'number_of_offspring' => 2,
    ]);
    BreedingRecord::factory()->for($farm)->for($doe, 'doe')->completed()->create([
        'mating_date' => now()->subMonth(),
        'number_of_offspring' => 4,
    ]);
    BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create([
        'mating_date' => now(),
        'actual_kidding_date' => null,
    ]);
    BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create([
        'mating_date' => now()->subMonths(9),
    ]);

    $result = (new GetBreedingSummaryAction)->handle(6);

    expect($result['totalMatings'])->toBe(3)
        ->and($result['completedCount'])->toBe(2)
        ->and($result['successRate'])->toBe(66.67)
        ->and($result['averageOffspring'])->toBe(3.0);
});

test('success rate and average offspring are null-safe when there are no matings', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $result = (new GetBreedingSummaryAction)->handle(6);

    expect($result['totalMatings'])->toBe(0)
        ->and($result['successRate'])->toBe(0.0)
        ->and($result['averageOffspring'])->toBeNull();
});

test('a wider range includes matings that a narrower range excludes', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $doe = Animal::factory()->for($farm)->create();
    BreedingRecord::factory()->for($farm)->for($doe, 'doe')->create(['mating_date' => now()->subMonths(8)]);

    expect((new GetBreedingSummaryAction)->handle(6)['totalMatings'])->toBe(0)
        ->and((new GetBreedingSummaryAction)->handle(12)['totalMatings'])->toBe(1);
});

test('it does not include breeding records from another farm', function () {
    $otherFarm = Farm::factory()->create();
    $otherDoe = Animal::factory()->for($otherFarm)->create();
    BreedingRecord::factory()->for($otherFarm)->for($otherDoe, 'doe')->create(['mating_date' => now()]);

    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $result = (new GetBreedingSummaryAction)->handle(6);

    expect($result['totalMatings'])->toBe(0);
});
