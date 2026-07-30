<?php

namespace App\Livewire\WeightLogs;

use App\Actions\RecalculateAnimalCurrentWeightAction;
use App\Concerns\WeightLogValidationRules;
use App\Models\Animal;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class WeightLogFormModal extends Component
{
    use WeightLogValidationRules;

    public ?Animal $animal = null;

    public bool $show = false;

    public ?int $animalId = null;

    public ?int $weightLogId = null;

    public string $weight = '';

    public string $recorded_at = '';

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
     * Open the modal to create or edit a weight log.
     *
     * Pass only `$animalId` to create a new log for that animal, or both
     * `$animalId` and `$weightLogId` to edit an existing log. Passing
     * neither opens a fresh, fully unbound create form.
     */
    #[On('open-weight-log-modal')]
    public function open(?int $weightLogId = null, ?int $animalId = null): void
    {
        $this->animal = $animalId ? Animal::findOrFail($animalId) : null;

        if ($this->animal) {
            Gate::authorize('update', $this->animal);
        } else {
            Gate::authorize('create', Animal::class);
        }

        $this->reset('weightLogId', 'weight', 'recorded_at');
        $this->resetValidation();

        $this->animalId = $this->animal?->id;

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
        $rules = [
            ...$this->weightLogRules(),
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

        if ($this->weightLogId) {
            $animal->weightLogs()->whereKey($this->weightLogId)->firstOrFail()->update($validated);
        } else {
            $animal->weightLogs()->create($validated);
        }

        $recalculateAnimalCurrentWeight->handle($animal);

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
