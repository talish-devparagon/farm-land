<?php

namespace Database\Seeders;

use App\Enums\AnimalSex;
use App\Models\BreedingRecord;
use App\Models\Farm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BreedingRecordSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Farm::with('animals')->get()->each(function (Farm $farm): void {
            $does = $farm->animals->where('sex', AnimalSex::Female);
            $bucks = $farm->animals->where('sex', AnimalSex::Male);

            if ($does->isEmpty()) {
                return;
            }

            $does->random(min(3, $does->count()))->each(function (mixed $doe) use ($farm, $bucks): void {
                $factory = BreedingRecord::factory()->for($farm)->for($doe, 'doe');

                if ($bucks->isNotEmpty()) {
                    $factory = $factory->for($bucks->random(), 'buck');
                }

                if (fake()->boolean(50)) {
                    $factory = $factory->completed();
                }

                $factory->create();
            });
        });
    }
}
