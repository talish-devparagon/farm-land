<?php

use App\Livewire\Dashboard\UpcomingHealthReminders;
use App\Models\Animal;
use App\Models\HealthRecord;
use Livewire\Livewire;

test('a due-soon health record shows its animal tag number', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create(['tag_number' => 'TAG-1001']);
    HealthRecord::factory()->for($animal)->for($user->farm)->dueSoon()->create();

    Livewire::test(UpcomingHealthReminders::class)
        ->assertSee('TAG-1001');
});

test('empty state copy is shown when nothing is due', function () {
    $this->actingAsFarmOwner();

    Livewire::test(UpcomingHealthReminders::class)
        ->assertSee('Nothing due in the next 30 days.');
});

test('records computed property returns health records due in the next 30 days', function () {
    $user = $this->actingAsFarmOwner();
    $animal = Animal::factory()->for($user->farm)->create();
    HealthRecord::factory()->for($animal)->for($user->farm)->dueSoon()->create();
    HealthRecord::factory()->for($animal)->for($user->farm)->create(['next_due_date' => null]);

    $records = Livewire::test(UpcomingHealthReminders::class)->instance()->records();

    expect($records)->toHaveCount(1);
});
