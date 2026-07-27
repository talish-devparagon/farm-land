<?php

namespace Database\Factories;

use App\Enums\AnimalSex;
use App\Models\Animal;
use App\Models\BreedingRecord;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<BreedingRecord>
 */
class BreedingRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $matingDate = fake()->dateTimeBetween('-4 months', '-1 month');

        return [
            'doe_id' => Animal::factory()->state(['sex' => AnimalSex::Female]),
            'buck_id' => null,
            'mating_date' => $matingDate,
            'expected_kidding_date' => (clone $matingDate)->modify('+150 days'),
            'actual_kidding_date' => null,
            'number_of_offspring' => null,
        ];
    }

    /**
     * Indicate that the kidding has already occurred, with offspring recorded.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $kiddingDate = Carbon::parse($attributes['mating_date'])->addDays(fake()->numberBetween(145, 155));

            return [
                'actual_kidding_date' => $kiddingDate,
                'number_of_offspring' => fake()->numberBetween(1, 3),
            ];
        });
    }
}
