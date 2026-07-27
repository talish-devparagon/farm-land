<?php

use App\Enums\FarmStatus;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

test('farm casts status to enum and defaults to active', function () {
    $farm = Farm::factory()->create();

    expect($farm->status)->toBeInstanceOf(FarmStatus::class)
        ->and($farm->status)->toBe(FarmStatus::Active);
});

test('farm belongs to an owner', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);

    expect($farm->owner)->toBeInstanceOf(User::class)
        ->and($farm->owner->id)->toBe($owner->id);
});

test('farm has many animals', function () {
    $farm = Farm::factory()->create();
    Animal::factory()->count(3)->for($farm)->create();

    expect($farm->animals())->toBeInstanceOf(HasMany::class)
        ->and($farm->animals()->count())->toBe(3);
});

test('farm is soft deletable', function () {
    $farm = Farm::factory()->create();

    $farm->delete();

    expect(Farm::find($farm->id))->toBeNull()
        ->and(Farm::withTrashed()->find($farm->id))->not->toBeNull();
});
