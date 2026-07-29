<?php

namespace App\Actions;

use App\Models\BreedingRecord;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Collection;

class GetUpcomingRemindersAction
{
    /**
     * Get health and breeding records due within the next 30 days.
     *
     * @return array{healthRecords: Collection<int, HealthRecord>, breedingRecords: Collection<int, BreedingRecord>}
     */
    public function handle(): array
    {
        return [
            'healthRecords' => $this->healthRecords(),
            'breedingRecords' => $this->breedingRecords(),
        ];
    }

    /**
     * Get health records due within the next 30 days.
     *
     * @return Collection<int, HealthRecord>
     */
    public function healthRecords(): Collection
    {
        return HealthRecord::query()
            ->with('animal')
            ->whereHas('animal')
            ->whereBetween('next_due_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('next_due_date')
            ->get();
    }

    /**
     * Get breeding records due within the next 30 days.
     *
     * @return Collection<int, BreedingRecord>
     */
    public function breedingRecords(): Collection
    {
        return BreedingRecord::query()
            ->with('doe')
            ->whereHas('doe')
            ->whereNull('actual_kidding_date')
            ->whereBetween('expected_kidding_date', [now()->toDateString(), now()->addDays(30)->toDateString()])
            ->orderBy('expected_kidding_date')
            ->get();
    }
}
