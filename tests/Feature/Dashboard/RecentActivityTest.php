<?php

use App\Livewire\Dashboard\RecentActivity;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\HealthRecord;
use Livewire\Livewire;

test('empty state copy is shown when there is no recent activity', function () {
    $this->actingAsFarmOwner();

    Livewire::test(RecentActivity::class)
        ->assertSee('No recent activity yet.');
});

test('recent activity merges animal, health, and breeding events sorted by date descending with icon and pre-resolved color classes', function () {
    $user = $this->actingAsFarmOwner();

    $animal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-3003', 'created_at' => now()->subDays(3)]);
    HealthRecord::factory()->for($animal)->for($user->farm)->create(['date' => now()->subDays(2)]);
    BreedingRecord::factory()->for($user->farm)->for($animal, 'doe')->create(['mating_date' => now()->subDay()]);

    $activities = Livewire::test(RecentActivity::class)->instance()->activities();

    expect($activities)->toHaveCount(3);

    $types = $activities->pluck('type')->all();
    expect($types)->toBe(['breeding_record', 'health_record', 'animal_added']);

    foreach ($activities as $activity) {
        expect($activity)->toHaveKeys(['type', 'label', 'date', 'icon', 'iconWrapClass', 'iconTextClass']);
    }

    expect($activities->firstWhere('type', 'animal_added'))->toMatchArray([
        'icon' => 'user-plus',
        'iconWrapClass' => 'bg-cyan-500/10',
        'iconTextClass' => 'text-cyan-600 dark:text-cyan-400',
    ]);
    expect($activities->firstWhere('type', 'health_record'))->toMatchArray([
        'icon' => 'shield-check',
        'iconWrapClass' => 'bg-green-500/10',
        'iconTextClass' => 'text-green-600 dark:text-green-400',
    ]);
    expect($activities->firstWhere('type', 'breeding_record'))->toMatchArray([
        'icon' => 'sparkles',
        'iconWrapClass' => 'bg-rose-500/10',
        'iconTextClass' => 'text-rose-600 dark:text-rose-400',
    ]);
});

test('recent activity labels reference the animal tag number', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-4004']);

    Livewire::test(RecentActivity::class)
        ->assertSee('TAG-4004');
});
