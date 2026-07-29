<?php

use App\Enums\HealthRecordType;
use App\Livewire\Animals\AnimalShow;
use App\Models\Animal;
use Livewire\Livewire;

test('shows the animal tag number, breed, and status', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create([
        'tag_number' => 'TAG-123',
        'breed' => 'Boer',
    ]);

    Livewire::test(AnimalShow::class, ['animal' => $animal])
        ->assertSee('TAG-123')
        ->assertSee('Boer')
        ->assertSee(ucfirst($animal->status->value));
});

test('shows health records on the health tab', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $animal->healthRecords()->create([
        'type' => HealthRecordType::Vaccination,
        'description' => 'Annual shots',
        'date' => now(),
    ]);

    Livewire::test(AnimalShow::class, ['animal' => $animal])
        ->set('activeTab', 'health')
        ->assertSee('Vaccination');
});

test('shows offspring on the offspring tab', function () {
    $user = $this->actingAsFarmOwner();
    $mother = Animal::factory()->for($user->farm)->create();
    $child = Animal::factory()->for($user->farm)->create(['mother_id' => $mother->id, 'tag_number' => 'KID-001']);

    Livewire::test(AnimalShow::class, ['animal' => $mother])
        ->set('activeTab', 'offspring')
        ->assertSee('KID-001');

    expect($child->mother_id)->toBe($mother->id);
});
