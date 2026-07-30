<?php

namespace App\Livewire\BreedingRecords;

use App\Enums\AnimalSex;
use App\Models\Animal;
use App\Models\BreedingRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Breeding Records')]
class BreedingRecordsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $doeId = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Animal::class);
    }

    /**
     * @return LengthAwarePaginator<int, BreedingRecord>
     */
    #[Computed]
    public function breedingRecords(): LengthAwarePaginator
    {
        return BreedingRecord::query()
            ->with(['doe', 'buck'])
            ->when($this->search, fn ($query) => $query->where(function ($query): void {
                $query->whereHas('doe', function ($query): void {
                    $query->where('tag_number', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                })->orWhereHas('buck', function ($query): void {
                    $query->where('tag_number', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                });
            }))
            ->when($this->status === 'pending', fn ($query) => $query->whereNull('actual_kidding_date'))
            ->when($this->status === 'complete', fn ($query) => $query->whereNotNull('actual_kidding_date'))
            ->when($this->doeId, fn ($query) => $query->where('doe_id', $this->doeId))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('mating_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('mating_date', '<=', $this->dateTo))
            ->orderByDesc('mating_date')
            ->paginate(15);
    }

    /**
     * Doe picker dropdown options, restricted to female animals on the current farm.
     *
     * @return Collection<int, Animal>
     */
    #[Computed]
    public function doeOptions(): Collection
    {
        return Animal::query()
            ->where('sex', AnimalSex::Female)
            ->orderBy('tag_number')
            ->get(['id', 'tag_number', 'name']);
    }

    /**
     * Status filter options, derived from whether the actual kidding date is set.
     *
     * @return array<string, string>
     */
    #[Computed]
    public function statuses(): array
    {
        return [
            'pending' => 'Pending',
            'complete' => 'Complete',
        ];
    }

    /**
     * Open the create/edit breeding record modal.
     *
     * Pass only `$doeId` to create a new record for that doe, or both
     * `$doeId` and `$breedingRecordId` to edit an existing record.
     */
    public function openBreedingRecordModal(?int $doeId = null, ?int $breedingRecordId = null): void
    {
        $this->dispatch('open-breeding-record-modal', doeId: $doeId, breedingRecordId: $breedingRecordId);
    }
}
