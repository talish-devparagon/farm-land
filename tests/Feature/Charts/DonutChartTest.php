<?php

use App\Livewire\Charts\DonutChart;
use Livewire\Livewire;

use function Livewire\store;

test('chart data derives labels, values, and color tokens from the segments', function () {
    $segments = [
        ['label' => 'Holstein', 'count' => 3, 'percent' => 60.0, 'color' => 'indigo'],
        ['label' => 'Jersey', 'count' => 2, 'percent' => 40.0, 'color' => 'amber'],
    ];

    $chartData = Livewire::test(DonutChart::class, ['segments' => $segments, 'total' => 5])->instance()->chartData();

    expect($chartData)->toBe([
        'labels' => ['Holstein', 'Jersey'],
        'values' => [3, 2],
        'colorTokens' => ['indigo', 'amber'],
    ]);
});

test('updating the segments dispatches a chart data updated event', function () {
    $component = Livewire::test(DonutChart::class, ['segments' => [], 'total' => 0])->instance();

    $newSegments = [
        ['label' => 'Holstein', 'count' => 3, 'percent' => 100.0, 'color' => 'indigo'],
    ];

    $component->segments = $newSegments;
    $component->total = 3;
    $component->updated('segments');
    $component->updated('total');

    $dispatched = collect(store($component)->get('dispatched', []))->map->serialize();

    expect($dispatched)->toContain([
        'name' => 'chart-data-updated',
        'params' => [
            'chartData' => [
                'labels' => ['Holstein'],
                'values' => [3],
                'colorTokens' => ['indigo'],
            ],
            'total' => 3,
        ],
        'self' => true,
    ]);
});
