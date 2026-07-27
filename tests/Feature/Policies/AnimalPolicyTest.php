<?php

use App\Enums\AnimalSex;
use App\Models\Animal;
use App\Models\Farm;
use App\Models\User;

test('farm owner can view any, create, and view/update/delete/restore their own farm animals', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $owner = $owner->fresh();
    $animal = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);

    expect($owner->can('viewAny', Animal::class))->toBeTrue()
        ->and($owner->can('create', Animal::class))->toBeTrue()
        ->and($owner->can('view', $animal))->toBeTrue()
        ->and($owner->can('update', $animal))->toBeTrue()
        ->and($owner->can('delete', $animal))->toBeTrue()
        ->and($owner->can('restore', $animal))->toBeTrue()
        ->and($owner->can('forceDelete', $animal))->toBeFalse();
});

test('farm owner cannot view, update, delete, or restore another farm animal', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $owner = $owner->fresh();
    $otherFarm = Farm::factory()->create();
    $otherAnimal = Animal::factory()->for($otherFarm)->create();

    expect($owner->can('view', $otherAnimal))->toBeFalse()
        ->and($owner->can('update', $otherAnimal))->toBeFalse()
        ->and($owner->can('delete', $otherAnimal))->toBeFalse()
        ->and($owner->can('restore', $otherAnimal))->toBeFalse()
        ->and($owner->can('forceDelete', $otherAnimal))->toBeFalse();
});

test('farm owner without a farm cannot create animals', function () {
    $owner = User::factory()->create();

    expect($owner->can('create', Animal::class))->toBeFalse();
});

test('admin cannot view any, create, view, update, delete, or restore animals', function () {
    $admin = User::factory()->admin()->create();
    $farm = Farm::factory()->create();
    $animal = Animal::factory()->for($farm)->create();

    expect($admin->can('viewAny', Animal::class))->toBeFalse()
        ->and($admin->can('create', Animal::class))->toBeFalse()
        ->and($admin->can('view', $animal))->toBeFalse()
        ->and($admin->can('update', $animal))->toBeFalse()
        ->and($admin->can('delete', $animal))->toBeFalse()
        ->and($admin->can('restore', $animal))->toBeFalse()
        ->and($admin->can('forceDelete', $animal))->toBeFalse();
});
