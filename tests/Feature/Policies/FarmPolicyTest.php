<?php

use App\Models\Farm;
use App\Models\User;

test('farm owner can update their own farm', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);

    expect($owner->can('update', $farm))->toBeTrue();
});

test('farm owner cannot update another farm', function () {
    $owner = User::factory()->create();
    Farm::factory()->create(['owner_id' => $owner->id]);

    $otherFarm = Farm::factory()->create();

    expect($owner->can('update', $otherFarm))->toBeFalse();
});

test('admin can update any farm', function () {
    $admin = User::factory()->admin()->create();
    $farm = Farm::factory()->create();

    expect($admin->can('update', $farm))->toBeTrue();
});
