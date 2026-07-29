<?php

namespace App\Livewire\Dashboard;

use App\Actions\GetUpcomingRemindersAction;
use App\Models\BreedingRecord;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UpcomingBreedingReminders extends Component
{
    /**
     * @return Collection<int, BreedingRecord>
     */
    #[Computed]
    public function records(): Collection
    {
        return app(GetUpcomingRemindersAction::class)->breedingRecords();
    }
}
