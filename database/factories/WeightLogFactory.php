<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\WeightLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeightLog>
 */
class WeightLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'animal_id' => Animal::factory(),
            'weight' => fake()->randomFloat(2, 50, 900),
            'recorded_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
