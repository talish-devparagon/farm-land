<?php

namespace App\Livewire\WeightLogs;

use App\Models\Animal;
use App\Models\WeightLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Weight Logs')]
class WeightLogsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $animalId = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Animal::class);
    }

    /**
     * @return LengthAwarePaginator<int, WeightLog>
     */
    #[Computed]
    public function weightLogs(): LengthAwarePaginator
    {
        return WeightLog::query()
            ->with('animal')
            ->whereHas('animal', function ($query): void {
                $query->when($this->search, fn ($query) => $query->where(function ($query): void {
                    $query->where('tag_number', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                }));
            })
            ->when($this->animalId, fn ($query) => $query->where('animal_id', $this->animalId))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('recorded_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('recorded_at', '<=', $this->dateTo))
            ->orderByDesc('recorded_at')
            ->paginate(15);
    }

    /**
     * Animal picker dropdown options, restricted to the current farm.
     *
     * @return Collection<int, Animal>
     */
    #[Computed]
    public function animalOptions(): Collection
    {
        return Animal::query()
            ->orderBy('tag_number')
            ->get(['id', 'tag_number', 'name']);
    }
}
