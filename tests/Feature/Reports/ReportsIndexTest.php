<?php

use App\Enums\AnimalSex;
use App\Enums\UserRole;
use App\Livewire\Reports\ReportsIndex;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\Farm;
use App\Models\HealthRecord;
use App\Models\User;
use App\Models\WeightLog;
use Livewire\Livewire;

test('reports index page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('reports.index'))->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('reports.index'))->assertRedirect(route('login'));
});

test('default range months is 6', function () {
    $this->actingAsFarmOwner();

    Livewire::test(ReportsIndex::class)->assertSet('rangeMonths', 6);
});

test('available ranges are 3, 6, and 12', function () {
    $this->actingAsFarmOwner();

    $ranges = Livewire::test(ReportsIndex::class)->instance()->availableRanges();

    expect($ranges)->toBe([3, 6, 12]);
});

test('an invalid rangeMonths query value is clamped back to 6 on mount', function () {
    $this->actingAsFarmOwner();

    Livewire::withQueryParams(['rangeMonths' => 999])
        ->test(ReportsIndex::class)
        ->assertSet('rangeMonths', 6);
});

test('an invalid rangeMonths value set after mount is clamped back to 6', function () {
    $this->actingAsFarmOwner();

    Livewire::test(ReportsIndex::class)
        ->set('rangeMonths', 999)
        ->assertSet('rangeMonths', 6);
});

test('herd composition only reflects the current farm animals', function () {
    $user = $this->actingAsFarmOwner();
    Animal::factory()->for($user->farm)->create(['breed' => 'Holstein']);

    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create(['breed' => 'Jersey']);

    $composition = Livewire::test(ReportsIndex::class)->instance()->herdComposition();

    expect($composition['total'])->toBe(1)
        ->and($composition['byBreed'])->toBe(['Holstein' => 1]);
});

test('health summary only reflects the current farm health records', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    HealthRecord::factory()->for($animal)->for($user->farm)->create(['date' => now()]);

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    HealthRecord::factory()->for($otherAnimal)->for($otherFarm)->create(['date' => now()]);

    $summary = Livewire::test(ReportsIndex::class)->instance()->healthSummary();

    expect($summary['totalRecords'])->toBe(1);
});

test('breeding summary only reflects the current farm breeding records', function () {
    $user = $this->actingAsFarmOwner();
    $doe = Animal::factory()->for($user->farm)->create();
    BreedingRecord::factory()->for($user->farm)->for($doe, 'doe')->create(['mating_date' => now()]);

    $otherFarm = Farm::factory()->create();
    $otherDoe = Animal::factory()->for($otherFarm)->create();
    BreedingRecord::factory()->for($otherFarm)->for($otherDoe, 'doe')->create(['mating_date' => now()]);

    $summary = Livewire::test(ReportsIndex::class)->instance()->breedingSummary();

    expect($summary['totalMatings'])->toBe(1);
});

test('growth summary only reflects weight logs for the current farm animals', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    WeightLog::factory()->for($animal)->create(['recorded_at' => now()]);

    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();
    WeightLog::factory()->for($otherAnimal)->create(['recorded_at' => now()]);

    $summary = Livewire::test(ReportsIndex::class)->instance()->growthSummary();

    expect($summary['totalWeightLogs'])->toBe(1);
});

test('changing rangeMonths widens the data window used by the summaries', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    $oldRecord = HealthRecord::factory()->for($animal)->for($user->farm)->create([
        'date' => now()->subMonths(8),
    ]);

    $component = Livewire::test(ReportsIndex::class);

    expect($component->instance()->healthSummary()['totalRecords'])->toBe(0);

    $component->set('rangeMonths', 12);

    expect($component->instance()->healthSummary()['totalRecords'])->toBe(1);
});

test('a user who is not a farm owner cannot view reports', function () {
    $admin = User::factory()->create(['role' => UserRole::Admin]);
    $this->actingAs($admin);

    $this->get(route('reports.index'))->assertForbidden();
});

test('herd sex split reports counts and percentages for each sex', function () {
    $user = $this->actingAsFarmOwner();
    Animal::factory()->for($user->farm)->count(3)->create(['sex' => AnimalSex::Male]);
    Animal::factory()->for($user->farm)->create(['sex' => AnimalSex::Female]);

    $split = Livewire::test(ReportsIndex::class)->instance()->herdSexSplit();

    expect($split)->toBe(['male' => 3, 'female' => 1, 'malePercent' => 75.0, 'femalePercent' => 25.0]);
});

test('herd age bars exclude brackets with no animals', function () {
    $user = $this->actingAsFarmOwner();
    Animal::factory()->for($user->farm)->create(['date_of_birth' => now()->subMonths(2)]);

    $bars = Livewire::test(ReportsIndex::class)->instance()->herdAgeBars();

    expect($bars->pluck('label')->all())->toBe(['0-6 months']);
});

test('herd breed segments bucket breeds into top 4 plus other', function () {
    $user = $this->actingAsFarmOwner();
    Animal::factory()->for($user->farm)->count(3)->create(['breed' => 'Holstein']);
    Animal::factory()->for($user->farm)->count(2)->create(['breed' => 'Jersey']);

    $segments = Livewire::test(ReportsIndex::class)->instance()->herdBreedSegments();

    expect($segments->pluck('label')->all())->toContain('Holstein', 'Jersey');
    foreach ($segments as $segment) {
        expect($segment)->toHaveKeys(['label', 'count', 'percent', 'color']);
    }
});
