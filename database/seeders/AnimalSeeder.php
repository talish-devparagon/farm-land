<?php

namespace Database\Seeders;

use App\Models\Animal;
use App\Models\Farm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnimalSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Farm::all()->each(function (Farm $farm): void {
            Animal::factory()
                ->count(fake()->numberBetween(8, 15))
                ->for($farm)
                ->create();
        });
    }
}
