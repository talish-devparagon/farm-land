<?php

use App\Enums\UserRole;
use App\Models\Farm;
use App\Models\User;

test('user casts role to enum and defaults to farm owner', function () {
    $user = User::factory()->create();

    expect($user->role)->toBeInstanceOf(UserRole::class)
        ->and($user->role)->toBe(UserRole::FarmOwner)
        ->and($user->isFarmOwner())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse();
});

test('admin factory state sets admin role', function () {
    $user = User::factory()->admin()->create();

    expect($user->role)->toBe(UserRole::Admin)
        ->and($user->isAdmin())->toBeTrue()
        ->and($user->isFarmOwner())->toBeFalse();
});

test('user has one farm via owner_id', function () {
    $user = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $user->id]);

    expect($user->farm)->toBeInstanceOf(Farm::class)
        ->and($user->farm->id)->toBe($farm->id);
});
