<?php

namespace App\Livewire\Animals;

use App\Actions\RecalculateAnimalCurrentWeightAction;
use App\Models\Animal;
use App\Models\BreedingRecord;
use App\Models\HealthRecord;
use App\Models\WeightLog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Animal')]
class AnimalShow extends Component
{
    public Animal $animal;

    public string $activeTab = 'health';

    public string $weight = '';

    public string $recorded_at = '';

    public function mount(Animal $animal): void
    {
        Gate::authorize('view', $animal);

        $this->animal = $animal;
    }

    public function logWeight(RecalculateAnimalCurrentWeightAction $recalculateAnimalCurrentWeight): void
    {
        Gate::authorize('update', $this->animal);

        $this->validate([
            'weight' => ['required', 'numeric', 'min:0'],
            'recorded_at' => ['required', 'date', 'before_or_equal:today'],
        ]);

        $this->animal->weightLogs()->create([
            'weight' => $this->weight,
            'recorded_at' => $this->recorded_at,
        ]);

        $recalculateAnimalCurrentWeight->handle($this->animal);

        $this->animal->refresh();

        $this->reset('weight', 'recorded_at');

        unset($this->weightLogs);
    }

    public function delete(): void
    {
        Gate::authorize('delete', $this->animal);

        $this->animal->delete();

        $this->redirectRoute('animals.index', navigate: true);
    }

    public function openHealthRecordModal(?int $healthRecordId = null): void
    {
        $this->dispatch('open-health-record-modal', healthRecordId: $healthRecordId);
    }

    public function openBreedingRecordModal(?int $id = null): void
    {
        $this->dispatch('open-breeding-record-modal', id: $id);
    }

    /**
     * @return Collection<int, HealthRecord>
     */
    #[Computed]
    public function healthRecords(): Collection
    {
        return $this->animal->healthRecords()->latest('date')->get();
    }

    /**
     * @return Collection<int, BreedingRecord>
     */
    #[Computed]
    public function breedingRecords(): Collection
    {
        return $this->animal->breedingRecordsAsDoe()->latest('mating_date')->get();
    }

    /**
     * @return Collection<int, WeightLog>
     */
    #[Computed]
    public function weightLogs(): Collection
    {
        return $this->animal->weightLogs()->latest('recorded_at')->get();
    }

    /**
     * @return Collection<int, Animal>
     */
    #[Computed]
    public function offspring(): Collection
    {
        return $this->animal->offspring();
    }

    #[Computed]
    public function mother(): ?Animal
    {
        return $this->animal->mother;
    }

    #[Computed]
    public function father(): ?Animal
    {
        return $this->animal->father;
    }
}
