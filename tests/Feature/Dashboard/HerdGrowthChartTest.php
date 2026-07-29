<?php

use App\Livewire\Dashboard\HerdGrowthChart;
use App\Models\Animal;
use Livewire\Livewire;

test('herd growth counts new animals per month for the last 6 months', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->count(2)->create(['created_at' => now()]);
    Animal::factory()->for($user->farm)->create(['created_at' => now()->subMonths(2)]);

    $growth = Livewire::test(HerdGrowthChart::class)->instance()->herdGrowth();

    expect($growth)->toHaveCount(6)
        ->and(array_keys($growth))->toBe(collect(range(5, 0))->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset)->format('M'))->all())
        ->and(array_values($growth)[5])->toBe(2)
        ->and(array_values($growth)[3])->toBe(1);
});

test('herd growth bars are shaped with position and size ready for the view', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->count(3)->create(['created_at' => now()]);

    $bars = Livewire::test(HerdGrowthChart::class)->instance()->bars();

    expect($bars)->toHaveCount(6);

    foreach ($bars as $bar) {
        expect($bar)->toHaveKeys(['label', 'value', 'x', 'y', 'width', 'height']);
    }

    $lastBar = $bars->last();
    expect($lastBar['value'])->toBe(3)
        ->and($lastBar['height'])->toBe(82.0)
        ->and($lastBar['y'])->toBe(16.0);

    $emptyBar = $bars->first();
    expect($emptyBar['value'])->toBe(0)
        ->and($emptyBar['height'])->toBe(0.0);
});

test('herd growth excludes soft-deleted animals', function () {
    $user = $this->actingAsFarmOwner();

    Animal::factory()->for($user->farm)->create(['created_at' => now()])->delete();

    $growth = Livewire::test(HerdGrowthChart::class)->instance()->herdGrowth();

    expect(array_sum($growth))->toBe(0);
});
