<?php

namespace Database\Factories;

use App\Enums\HealthRecordType;
use App\Models\Animal;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthRecord>
 */
class HealthRecordFactory extends Factory
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
            'type' => fake()->randomElement(HealthRecordType::cases()),
            'description' => fake()->sentence(),
            'date' => fake()->dateTimeBetween('-1 year', 'now'),
            'next_due_date' => null,
        ];
    }

    /**
     * Indicate that this record has a next due date within the next 30 days.
     */
    public function dueSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'next_due_date' => fake()->dateTimeBetween('now', '+30 days'),
        ]);
    }
}
