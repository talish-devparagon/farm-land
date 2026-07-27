<?php

namespace Database\Factories;

use App\Enums\FarmStatus;
use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Farm>
 */
class FarmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'name' => fake()->company().' Farm',
            'location' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'status' => FarmStatus::Active,
        ];
    }
}
