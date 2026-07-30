<?php

use App\Actions\Reports\GetHerdCompositionAction;
use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\User;

test('it breaks the herd down by breed, status, sex, and age bracket', function () {
    $farm = Farm::factory()->create();

    Animal::factory()->for($farm)->create([
        'breed' => 'Holstein',
        'status' => AnimalStatus::Alive,
        'sex' => AnimalSex::Female,
        'date_of_birth' => now()->subMonths(3),
    ]);
    Animal::factory()->for($farm)->create([
        'breed' => 'Holstein',
        'status' => AnimalStatus::Sold,
        'sex' => AnimalSex::Male,
        'date_of_birth' => now()->subMonths(18),
    ]);
    Animal::factory()->for($farm)->create([
        'breed' => null,
        'status' => AnimalStatus::Alive,
        'sex' => AnimalSex::Female,
        'date_of_birth' => null,
    ]);

    $result = (new GetHerdCompositionAction)->handle();

    expect($result['total'])->toBe(3)
        ->and($result['byBreed'])->toBe(['Holstein' => 2, 'Unknown' => 1])
        ->and($result['byStatus'])->toBe([
            AnimalStatus::Alive->value => 2,
            AnimalStatus::Sold->value => 1,
        ])
        ->and($result['bySex'])->toBe([
            AnimalSex::Female->value => 2,
            AnimalSex::Male->value => 1,
        ])
        ->and($result['byAgeBracket'])->toBe([
            '0-6 months' => 1,
            '6-12 months' => 0,
            '1-2 years' => 1,
            '2+ years' => 0,
            'Unknown' => 1,
        ]);
});

test('age brackets correctly bucket every boundary', function () {
    $farm = Farm::factory()->create();

    Animal::factory()->for($farm)->create(['date_of_birth' => now()->subMonths(5)]);
    Animal::factory()->for($farm)->create(['date_of_birth' => now()->subMonths(10)]);
    Animal::factory()->for($farm)->create(['date_of_birth' => now()->subMonths(20)]);
    Animal::factory()->for($farm)->create(['date_of_birth' => now()->subMonths(36)]);

    $result = (new GetHerdCompositionAction)->handle();

    expect($result['byAgeBracket'])->toBe([
        '0-6 months' => 1,
        '6-12 months' => 1,
        '1-2 years' => 1,
        '2+ years' => 1,
        'Unknown' => 0,
    ]);
});

test('it does not include animals from another farm', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    test()->actingAs($owner->fresh());

    Animal::factory()->for($farm)->create();

    $otherFarm = Farm::factory()->create();
    Animal::factory()->for($otherFarm)->create();

    $result = (new GetHerdCompositionAction)->handle();

    expect($result['total'])->toBe(1);
});
