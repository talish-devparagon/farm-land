<?php

namespace App\Livewire;

use App\Actions\GetUpcomingRemindersAction;
use App\Models\BreedingRecord;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Upcoming')]
class Upcoming extends Component
{
    /**
     * @var array{healthRecords: Collection<int, HealthRecord>, breedingRecords: Collection<int, BreedingRecord>}|null
     */
    private ?array $reminders = null;

    /**
     * @return Collection<int, HealthRecord>
     */
    #[Computed]
    public function upcomingHealthRecords(): Collection
    {
        return $this->reminders()['healthRecords'];
    }

    /**
     * @return Collection<int, BreedingRecord>
     */
    #[Computed]
    public function upcomingBreedingRecords(): Collection
    {
        return $this->reminders()['breedingRecords'];
    }

    /**
     * @return array{healthRecords: Collection<int, HealthRecord>, breedingRecords: Collection<int, BreedingRecord>}
     */
    private function reminders(): array
    {
        return $this->reminders ??= app(GetUpcomingRemindersAction::class)->handle();
    }
}
