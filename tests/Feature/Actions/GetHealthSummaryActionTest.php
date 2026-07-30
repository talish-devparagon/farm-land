<?php

use App\Actions\Reports\GetHealthSummaryAction;
use App\Enums\HealthRecordType;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\HealthRecord;
use App\Models\User;

test('it counts health records by type within the selected range', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();

    HealthRecord::factory()->for($animal)->for($farm)->create(['type' => HealthRecordType::Vaccination, 'date' => now()]);
    HealthRecord::factory()->for($animal)->for($farm)->create(['type' => HealthRecordType::Vaccination, 'date' => now()->subMonth()]);
    HealthRecord::factory()->for($animal)->for($farm)->create(['type' => HealthRecordType::Treatment, 'date' => now()]);
    HealthRecord::factory()->for($animal)->for($farm)->create(['type' => HealthRecordType::VetVisit, 'date' => now()->subMonths(8)]);

    $result = (new GetHealthSummaryAction)->handle(6);

    expect($result['totalRecords'])->toBe(3)
        ->and($result['byType'])->toBe([
            HealthRecordType::Vaccination->value => 2,
            HealthRecordType::Treatment->value => 1,
            HealthRecordType::VetVisit->value => 0,
        ]);
});

test('a wider range includes records that a narrower range excludes', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();
    HealthRecord::factory()->for($animal)->for($farm)->create(['date' => now()->subMonths(8)]);

    expect((new GetHealthSummaryAction)->handle(3)['totalRecords'])->toBe(0)
        ->and((new GetHealthSummaryAction)->handle(6)['totalRecords'])->toBe(0)
        ->and((new GetHealthSummaryAction)->handle(12)['totalRecords'])->toBe(1);
});

test('overdue and upcoming counts reflect right now regardless of the selected range', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $animal = Animal::factory()->for($farm)->create();

    HealthRecord::factory()->for($animal)->for($farm)->create(['next_due_date' => now()->subDays(5)]);
    HealthRecord::factory()->for($animal)->for($farm)->dueSoon()->create();
    HealthRecord::factory()->for($animal)->for($farm)->create(['next_due_date' => now()->addDays(90)]);

    $result = (new GetHealthSummaryAction)->handle(3);

    expect($result['overdueCount'])->toBe(1)
        ->and($result['upcomingCount'])->toBe(1);
});

test('it does not include health records from another farm', function () {
    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    HealthRecord::factory()->for($otherAnimal)->for($otherFarm)->create(['date' => now()]);

    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    $result = (new GetHealthSummaryAction)->handle(6);

    expect($result['totalRecords'])->toBe(0);
});
