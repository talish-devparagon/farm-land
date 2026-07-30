<?php

namespace App\Livewire\Animals;

use App\Concerns\HealthRecordValidationRules;
use App\Models\Animal;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class HealthRecordFormModal extends Component
{
    use HealthRecordValidationRules;

    public ?Animal $animal = null;

    public bool $show = false;

    public ?int $healthRecordId = null;

    public string $type = '';

    public string $description = '';

    public string $date = '';

    public ?string $next_due_date = null;

    public ?string $notes = null;

    /**
     * Open the modal to create or edit a health record.
     *
     * When `$animalId` is provided, the animal is resolved from it (used
     * when this component is rendered without a bound `$animal`, e.g. on
     * the farm-wide health records index). Otherwise, the `$animal`
     * already bound to this component (e.g. via `AnimalShow`) is used.
     *
     * `$healthRecordId` remains the first parameter (rather than
     * `$animalId`) so existing single-argument callers (e.g.
     * `open($healthRecordId)` from `AnimalShow`) keep working unchanged.
     */
    #[On('open-health-record-modal')]
    public function open(?int $healthRecordId = null, ?int $animalId = null): void
    {
        if ($animalId) {
            $this->animal = Animal::findOrFail($animalId);
        }

        Gate::authorize('update', $this->animal);

        $this->reset('healthRecordId', 'type', 'description', 'date', 'next_due_date', 'notes');
        $this->resetValidation();

        if ($healthRecordId) {
            $healthRecord = $this->animal->healthRecords()->findOrFail($healthRecordId);

            $this->healthRecordId = $healthRecord->id;
            $this->type = $healthRecord->type->value;
            $this->description = $healthRecord->description;
            $this->date = $healthRecord->date->toDateString();
            $this->next_due_date = $healthRecord->next_due_date?->toDateString();
            $this->notes = $healthRecord->notes;
        }

        $this->show = true;
    }

    public function save(): void
    {
        Gate::authorize('update', $this->animal);

        $validated = $this->validate($this->healthRecordRules());

        if ($this->healthRecordId) {
            $this->animal->healthRecords()->whereKey($this->healthRecordId)->firstOrFail()->update($validated);
        } else {
            $this->animal->healthRecords()->create($validated);
        }

        $this->show = false;

        $this->dispatch('health-record-saved');
    }

    public function delete(): void
    {
        Gate::authorize('update', $this->animal);

        if ($this->healthRecordId) {
            $this->animal->healthRecords()->whereKey($this->healthRecordId)->delete();
        }

        $this->show = false;

        $this->dispatch('health-record-saved');
    }
}
