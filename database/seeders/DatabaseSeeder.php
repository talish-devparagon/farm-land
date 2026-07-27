<?php

namespace Database\Seeders;

use App\Models\Farm;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $owner = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $farm = Farm::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'Test Farm',
        ]);

        $owner->update(['farm_id' => $farm->id]);

        $this->call([
            FarmSeeder::class,
            AnimalSeeder::class,
            WeightLogSeeder::class,
            HealthRecordSeeder::class,
            BreedingRecordSeeder::class,
        ]);
    }
}
