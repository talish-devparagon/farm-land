<?php

use App\Livewire\Charts\LineChart;
use Livewire\Livewire;

use function Livewire\store;

test('chart data derives labels and values from the series', function () {
    $series = ['Jan' => 100.0, 'Feb' => null, 'Mar' => 150.5];

    $chartData = Livewire::test(LineChart::class, ['series' => $series])->instance()->chartData();

    expect($chartData)->toBe([
        'labels' => ['Jan', 'Feb', 'Mar'],
        'values' => [100.0, null, 150.5],
    ]);
});

test('has data is false when every entry in the series is null', function () {
    $series = ['Jan' => null, 'Feb' => null];

    $hasData = Livewire::test(LineChart::class, ['series' => $series])->instance()->hasData();

    expect($hasData)->toBeFalse();
});

test('has data is true when at least one entry has a value', function () {
    $series = ['Jan' => null, 'Feb' => 42.0];

    $hasData = Livewire::test(LineChart::class, ['series' => $series])->instance()->hasData();

    expect($hasData)->toBeTrue();
});

test('updating the series dispatches a chart data updated event', function () {
    $component = Livewire::test(LineChart::class, ['series' => ['Jan' => null]])->instance();

    $component->series = ['Jan' => null, 'Feb' => 42.0];
    $component->updated('series');

    $dispatched = collect(store($component)->get('dispatched', []))->map->serialize();

    expect($dispatched)->toContain([
        'name' => 'chart-data-updated',
        'params' => [
            'chartData' => [
                'labels' => ['Jan', 'Feb'],
                'values' => [null, 42.0],
            ],
            'hasData' => true,
        ],
        'self' => true,
    ]);
});
