<?php

namespace App\Livewire\Animals;

use App\Concerns\HealthRecordValidationRules;
use App\Models\Animal;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class HealthRecordFormModal extends Component
{
    use HealthRecordValidationRules;

    public ?Animal $animal = null;

    /**
     * Whether `$animal` was bound by a parent component (e.g. `AnimalShow`)
     * and must therefore stay fixed for the lifetime of this component.
     */
    public bool $animalLocked = false;

    public bool $show = false;

    public ?int $animalId = null;

    public ?int $healthRecordId = null;

    public string $type = '';

    public string $description = '';

    public string $date = '';

    public ?string $next_due_date = null;

    public ?string $notes = null;

    public function mount(?Animal $animal = null): void
    {
        $this->animal = $animal;
        $this->animalLocked = $animal !== null;
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
     * Open the modal to create or edit a health record.
     *
     * When `$animalId` is provided and the component is not locked to a
     * bound animal, the animal is resolved from it (used when this
     * component is rendered without a bound `$animal`, e.g. on the
     * farm-wide health records index). Otherwise, the `$animal` already
     * bound to this component (e.g. via `AnimalShow`) is used untouched.
     *
     * `$healthRecordId` remains the first parameter (rather than
     * `$animalId`) so existing single-argument callers (e.g.
     * `open($healthRecordId)` from `AnimalShow`) keep working unchanged.
     */
    #[On('open-health-record-modal')]
    public function open(?int $healthRecordId = null, ?int $animalId = null): void
    {
        if (! $this->animalLocked) {
            $this->animal = $animalId ? Animal::findOrFail($animalId) : null;
        }

        if ($this->animal) {
            Gate::authorize('update', $this->animal);
        } else {
            Gate::authorize('create', Animal::class);
        }

        $this->reset('healthRecordId', 'type', 'description', 'date', 'next_due_date', 'notes');
        $this->resetValidation();

        $this->animalId = $this->animal?->id;

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
        $rules = [
            ...$this->healthRecordRules(),
            'animalId' => [
                'required',
                'integer',
                Rule::exists(Animal::class, 'id')->where('farm_id', Auth::user()?->farm?->id),
            ],
        ];

        $validated = $this->validate($rules);

        $animalId = Arr::pull($validated, 'animalId');
        $animal = $this->animal ?? Animal::query()->findOrFail($animalId);

        Gate::authorize('update', $animal);

        if ($this->healthRecordId) {
            $animal->healthRecords()->whereKey($this->healthRecordId)->firstOrFail()->update($validated);
        } else {
            $animal->healthRecords()->create($validated);
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
