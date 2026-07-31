<?php

use App\Livewire\Charts\BarChart;
use Livewire\Livewire;

use function Livewire\store;

test('chart data derives labels and values from the series', function () {
    $series = ['Jan' => 2, 'Feb' => 5, 'Mar' => 0];

    $chartData = Livewire::test(BarChart::class, ['series' => $series])->instance()->chartData();

    expect($chartData)->toBe([
        'labels' => ['Jan', 'Feb', 'Mar'],
        'values' => [2, 5, 0],
    ]);
});

test('updating the series dispatches a chart data updated event', function () {
    $component = Livewire::test(BarChart::class, ['series' => ['Jan' => 1]])->instance();

    $component->series = ['Jan' => 1, 'Feb' => 4];
    $component->updated('series');

    $dispatched = collect(store($component)->get('dispatched', []))->map->serialize();

    expect($dispatched)->toContain([
        'name' => 'chart-data-updated',
        'params' => [
            'chartData' => [
                'labels' => ['Jan', 'Feb'],
                'values' => [1, 4],
            ],
        ],
        'self' => true,
    ]);
});
