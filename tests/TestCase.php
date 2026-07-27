<?php

namespace Tests;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;

abstract class TestCase extends BaseTestCase
{
    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    protected function actingAsFarmOwner(?Farm $farm = null): User
    {
        $user = User::factory()->create();
        $farm ??= Farm::factory()->create(['owner_id' => $user->id]);
        $user->update(['farm_id' => $farm->id]);

        $this->actingAs($user);

        return $user;
    }
}
