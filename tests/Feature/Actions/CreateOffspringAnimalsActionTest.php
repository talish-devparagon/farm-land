<?php

use App\Actions\CreateOffspringAnimalsAction;
use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\Farm;
use App\Models\User;

test('recording a kidding creates an animal for each offspring', function () {
    $owner = User::factory()->create();
    $farm = Farm::factory()->create(['owner_id' => $owner->id]);
    $owner->update(['farm_id' => $farm->id]);
    $this->actingAs($owner->fresh());

    $doe = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Female]);
    $buck = Animal::factory()->for($farm)->create(['sex' => AnimalSex::Male]);
    $kiddingDate = now()->toDateString();
    $breedingRecord = BreedingRecord::factory()->for($farm)->for($doe, 'doe')->for($buck, 'buck')->create([
        'actual_kidding_date' => $kiddingDate,
    ]);

    (new CreateOffspringAnimalsAction)->handle($breedingRecord, [
        ['tag_number' => 'KID-001', 'name' => 'Kid A', 'sex' => AnimalSex::Female->value],
        ['tag_number' => 'KID-002', 'name' => 'Kid B', 'sex' => AnimalSex::Male->value],
    ]);

    expect(Animal::where('mother_id', $doe->id)->count())->toBe(2);

    $kidA = Animal::where('tag_number', 'KID-001')->sole();
    expect($kidA->name)->toBe('Kid A')
        ->and($kidA->sex)->toBe(AnimalSex::Female)
        ->and($kidA->mother_id)->toBe($doe->id)
        ->and($kidA->father_id)->toBe($buck->id)
        ->and($kidA->date_of_birth->toDateString())->toBe($kiddingDate)
        ->and($kidA->status)->toBe(AnimalStatus::Alive)
        ->and($kidA->farm_id)->toBe($farm->id);
});
