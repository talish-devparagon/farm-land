<?php

use App\Enums\HealthRecordType;
use App\Livewire\Animals\HealthRecordFormModal;
use App\Models\Animal;
use Livewire\Livewire;

test('renders the health record type options', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();

    Livewire::test(HealthRecordFormModal::class, ['animal' => $animal])
        ->call('open')
        ->assertSee(ucfirst(HealthRecordType::Vaccination->value))
        ->assertSee(ucfirst(HealthRecordType::Treatment->value));
});

test('shows a delete button only when editing an existing record', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    $healthRecord = $animal->healthRecords()->create([
        'type' => HealthRecordType::Vaccination,
        'description' => 'Checkup',
        'date' => now(),
    ]);

    Livewire::test(HealthRecordFormModal::class, ['animal' => $animal])
        ->call('open')
        ->assertDontSee('Delete')
        ->call('open', $healthRecord->id)
        ->assertSee('Delete');
});
