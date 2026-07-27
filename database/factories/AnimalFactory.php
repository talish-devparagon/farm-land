<?php

namespace Database\Factories;

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use App\Models\Farm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Animal>
 */
class AnimalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'farm_id' => Farm::factory(),
            'tag_number' => fake()->unique()->numerify('TAG-####'),
            'name' => fake()->firstName(),
            'breed' => fake()->randomElement(['Holstein', 'Jersey', 'Angus', 'Hereford']),
            'sex' => fake()->randomElement(AnimalSex::cases()),
            'date_of_birth' => fake()->dateTimeBetween('-5 years', 'now'),
            'status' => AnimalStatus::Alive,
            'current_weight' => fake()->randomFloat(2, 50, 900),
        ];
    }
}
