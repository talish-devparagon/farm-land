<?php

namespace App\Livewire\HealthRecords;

use App\Enums\HealthRecordType;
use App\Models\Animal;
use App\Models\HealthRecord;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Health Records')]
class HealthRecordsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $type = '';

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
     * @return LengthAwarePaginator<int, HealthRecord>
     */
    #[Computed]
    public function healthRecords(): LengthAwarePaginator
    {
        return HealthRecord::query()
            ->with('animal')
            ->whereHas('animal', function ($query): void {
                $query->when($this->search, fn ($query) => $query->where(function ($query): void {
                    $query->where('tag_number', 'like', "%{$this->search}%")
                        ->orWhere('name', 'like', "%{$this->search}%");
                }));
            })
            ->when($this->type, fn ($query) => $query->where('type', $this->type))
            ->when($this->animalId, fn ($query) => $query->where('animal_id', $this->animalId))
            ->when($this->dateFrom, fn ($query) => $query->whereDate('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($query) => $query->whereDate('date', '<=', $this->dateTo))
            ->orderByDesc('date')
            ->paginate(15);
    }

    /**
     * Health record type filter options.
     *
     * @return array<int, HealthRecordType>
     */
    #[Computed]
    public function types(): array
    {
        return HealthRecordType::cases();
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

    /**
     * Open the create/edit health record modal.
     *
     * Pass only `$animalId` to create a new record for that animal, or both
     * `$animalId` and `$healthRecordId` to edit an existing record.
     */
    public function openHealthRecordModal(?int $animalId = null, ?int $healthRecordId = null): void
    {
        $this->dispatch('open-health-record-modal', animalId: $animalId, healthRecordId: $healthRecordId);
    }

    public function deleteHealthRecord(int $healthRecordId): void
    {
        $healthRecord = HealthRecord::with('animal')->findOrFail($healthRecordId);

        Gate::authorize('update', $healthRecord->animal);

        $healthRecord->delete();
    }
}
