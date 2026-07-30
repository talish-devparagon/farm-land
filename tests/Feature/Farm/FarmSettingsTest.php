<?php

use App\Enums\FarmStatus;
use App\Livewire\Farm\FarmSettings;
use App\Models\Farm;
use App\Models\User;
use Livewire\Livewire;

test('farm settings page is displayed', function () {
    $this->actingAsFarmOwner();

    $this->get(route('farm.edit'))->assertOk();
});

test('guests are redirected to login', function () {
    $this->get(route('farm.edit'))->assertRedirect(route('login'));
});

test('mounting populates the farm owners current farm details', function () {
    $user = $this->actingAsFarmOwner();
    $user->farm->update(['name' => 'Green Pastures', 'location' => 'Countryside', 'phone' => '555-0199']);
    $user = $user->fresh();

    Livewire::test(FarmSettings::class)
        ->assertSet('name', 'Green Pastures')
        ->assertSet('location', 'Countryside')
        ->assertSet('phone', '555-0199')
        ->assertSet('status', $user->farm->status->value);
});

test('farm owner can update their farm details', function () {
    $user = $this->actingAsFarmOwner();

    Livewire::test(FarmSettings::class)
        ->set('name', 'Updated Farm Name')
        ->set('location', 'New Location')
        ->set('phone', '555-0100')
        ->call('update')
        ->assertHasNoErrors();

    $user->farm->refresh();

    expect($user->farm->name)->toBe('Updated Farm Name')
        ->and($user->farm->location)->toBe('New Location')
        ->and($user->farm->phone)->toBe('555-0100');
});

test('farm name is required', function () {
    $this->actingAsFarmOwner();

    Livewire::test(FarmSettings::class)
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);
});

test('farm owner without a farm cannot access farm settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(FarmSettings::class)->assertForbidden();
});

test('regular farm owner cannot edit farm status', function () {
    $user = $this->actingAsFarmOwner();

    expect($user->farm->status)->toBe(FarmStatus::Active);

    Livewire::test(FarmSettings::class)
        ->assertSet('canEditStatus', false)
        ->set('status', FarmStatus::Suspended->value)
        ->call('update')
        ->assertHasNoErrors();

    expect($user->farm->fresh()->status)->toBe(FarmStatus::Active);
});

test('admin can edit farm status for a farm they own', function () {
    $admin = User::factory()->admin()->create();
    $farm = Farm::factory()->create(['owner_id' => $admin->id]);
    $admin->update(['farm_id' => $farm->id]);
    $admin = $admin->fresh();

    $this->actingAs($admin);

    Livewire::test(FarmSettings::class)
        ->assertSet('canEditStatus', true)
        ->set('status', FarmStatus::Suspended->value)
        ->call('update')
        ->assertHasNoErrors();

    expect($farm->fresh()->status)->toBe(FarmStatus::Suspended);
});
