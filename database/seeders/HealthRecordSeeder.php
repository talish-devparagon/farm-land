<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\HealthRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HealthRecordSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Animal::all()->each(function (Animal $animal): void {
            HealthRecord::factory()
                ->count(fake()->numberBetween(1, 3))
                ->for($animal)
                ->for($animal->farm)
                ->create();

            if (fake()->boolean(40)) {
                HealthRecord::factory()
                    ->for($animal)
                    ->for($animal->farm)
                    ->dueSoon()
                    ->create();
            }
        });
    }
}
