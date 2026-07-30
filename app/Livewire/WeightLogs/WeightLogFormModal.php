<?php

namespace App\Livewire\WeightLogs;

use App\Actions\RecalculateAnimalCurrentWeightAction;
use App\Concerns\WeightLogValidationRules;
use App\Models\Animal;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class WeightLogFormModal extends Component
{
    use WeightLogValidationRules;

    public ?Animal $animal = null;

    public bool $show = false;

    public ?int $weightLogId = null;

    public string $weight = '';

    public string $recorded_at = '';

    /**
     * Open the modal to create or edit a weight log.
     *
     * Pass only `$animalId` to create a new log for that animal, or both
     * `$animalId` and `$weightLogId` to edit an existing log.
     */
    #[On('open-weight-log-modal')]
    public function open(?int $weightLogId = null, ?int $animalId = null): void
    {
        if ($animalId) {
            $this->animal = Animal::findOrFail($animalId);
        }

        Gate::authorize('update', $this->animal);

        $this->reset('weightLogId', 'weight', 'recorded_at');
        $this->resetValidation();

        if ($weightLogId) {
            $weightLog = $this->animal->weightLogs()->findOrFail($weightLogId);

            $this->weightLogId = $weightLog->id;
            $this->weight = (string) $weightLog->weight;
            $this->recorded_at = $weightLog->recorded_at->toDateString();
        }

        $this->show = true;
    }

    public function save(RecalculateAnimalCurrentWeightAction $recalculateAnimalCurrentWeight): void
    {
        Gate::authorize('update', $this->animal);

        $validated = $this->validate($this->weightLogRules());

        if ($this->weightLogId) {
            $this->animal->weightLogs()->whereKey($this->weightLogId)->firstOrFail()->update($validated);
        } else {
            $this->animal->weightLogs()->create($validated);
        }

        $recalculateAnimalCurrentWeight->handle($this->animal);

        $this->show = false;

        $this->dispatch('weight-log-saved');
    }

    public function delete(RecalculateAnimalCurrentWeightAction $recalculateAnimalCurrentWeight): void
    {
        Gate::authorize('update', $this->animal);

        if ($this->weightLogId) {
            $this->animal->weightLogs()->whereKey($this->weightLogId)->delete();

            $recalculateAnimalCurrentWeight->handle($this->animal);
        }

        $this->show = false;

        $this->dispatch('weight-log-saved');
    }
}
