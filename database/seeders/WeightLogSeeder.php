<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\WeightLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeightLogSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Animal::all()->each(function (Animal $animal): void {
            WeightLog::factory()
                ->count(fake()->numberBetween(3, 6))
                ->for($animal)
                ->create();

            $animal->update([
                'current_weight' => $animal->weightLogs()->latest('recorded_at')->value('weight'),
            ]);
        });
    }
}
