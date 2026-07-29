<?php

namespace App\Livewire\Dashboard;

use App\Actions\GetUpcomingRemindersAction;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

class UpcomingHealthReminders extends Component
{
    /**
     * @return Collection<int, HealthRecord>
     */
    #[Computed]
    public function records(): Collection
    {
        return app(GetUpcomingRemindersAction::class)->healthRecords();
    }
}
