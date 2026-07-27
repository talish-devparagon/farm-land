<?php

use App\Models\Animal;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\QueryException;

test('farm scope limits a non-admin user to animals on their own farm', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);

    $otherFarm = Farm::factory()->create();

    Animal::factory()->for($farm)->count(2)->create();
    Animal::factory()->for($otherFarm)->count(3)->create();

    $this->actingAs($owner);

    expect(Animal::count())->toBe(2)
        ->and(Animal::all()->pluck('farm_id')->unique()->all())->toBe([$farm->id]);
});

test('farm scope does not filter an admin user', function () {
    $farm = Farm::factory()->create();
    $otherFarm = Farm::factory()->create();

    $admin = User::factory()->admin()->create();

    Animal::factory()->for($farm)->count(2)->create();
    Animal::factory()->for($otherFarm)->count(3)->create();

    $this->actingAs($admin);

    expect(Animal::count())->toBe(5);
});

test('belongs to farm auto-assigns farm_id from the authenticated non-admin user on creation', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);

    $this->actingAs($owner);

    $animal = Animal::factory()->make(['farm_id' => null]);
    $animal->save();

    expect($animal->fresh()->farm_id)->toBe($farm->id);
});

test('belongs to farm does not auto-assign farm_id for an admin user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    // animals.farm_id is a required column with no default, so an admin
    // creating one without an explicit farm_id must fail at the database
    // level — proving the creating hook skipped auto-assignment for admins.
    expect(fn () => Animal::factory()->make(['farm_id' => null])->save())
        ->toThrow(QueryException::class);
});
