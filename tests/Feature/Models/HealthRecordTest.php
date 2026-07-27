<?php

use App\Enums\HealthRecordType;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\HealthRecord;
use Carbon\CarbonInterface;

test('health record casts type and dates', function () {
    $animal = Animal::factory()->create();
    $healthRecord = HealthRecord::factory()
        ->for($animal)
        ->for($animal->farm)
        ->create(['type' => HealthRecordType::Vaccination]);

    expect($healthRecord->type)->toBeInstanceOf(HealthRecordType::class)
        ->and($healthRecord->type)->toBe(HealthRecordType::Vaccination)
        ->and($healthRecord->date)->toBeInstanceOf(CarbonInterface::class);
});

test('health record belongs to a farm and an animal', function () {
    $farm = Farm::factory()->create();
    $animal = Animal::factory()->for($farm)->create();
    $healthRecord = HealthRecord::factory()->for($animal)->for($farm)->create();

    expect($healthRecord->farm->id)->toBe($farm->id)
        ->and($healthRecord->animal->id)->toBe($animal->id);
});

test('dueSoon state sets next_due_date within the next 30 days', function () {
    $animal = Animal::factory()->create();
    $healthRecord = HealthRecord::factory()
        ->for($animal)
        ->for($animal->farm)
        ->dueSoon()
        ->create();

    expect($healthRecord->next_due_date)->not->toBeNull()
        ->and($healthRecord->next_due_date->greaterThanOrEqualTo(today()))->toBeTrue()
        ->and($healthRecord->next_due_date->lessThanOrEqualTo(today()->addDays(30)))->toBeTrue();
});
