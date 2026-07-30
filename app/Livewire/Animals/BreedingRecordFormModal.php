<?php

namespace App\Livewire\Animals;

use App\Actions\CreateOffspringAnimalsAction;
use App\Concerns\BreedingRecordValidationRules;
use App\Models\Animal;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Component;

class BreedingRecordFormModal extends Component
{
    use BreedingRecordValidationRules;

    public ?Animal $animal = null;

    public bool $show = false;

    public ?int $breedingRecordId = null;

    public ?int $buck_id = null;

    public string $mating_date = '';

    public string $expected_kidding_date = '';

    public ?string $actual_kidding_date = null;

    public ?string $notes = null;

    public bool $create_offspring = false;

    /**
     * @var array<int, array{tag_number: string, name: ?string, sex: string}>
     */
    public array $offspring = [];

    /**
     * Open the modal to create or edit a breeding record.
     *
     * When `$doeId` is provided, the doe is resolved from it (used when this
     * component is rendered without a bound `$animal`, e.g. on the farm-wide
     * breeding records index). Otherwise, the `$animal` already bound to this
     * component (e.g. via `AnimalShow`) is used.
     *
     * `$breedingRecordId` remains the first parameter (rather than `$doeId`)
     * so existing single-argument callers (e.g. `open($breedingRecordId)`
     * from `AnimalShow`) keep working unchanged.
     */
    #[On('open-breeding-record-modal')]
    public function open(?int $breedingRecordId = null, ?int $doeId = null): void
    {
        if ($doeId) {
            $this->animal = Animal::findOrFail($doeId);
        }

        Gate::authorize('update', $this->animal);

        $this->reset(
            'breedingRecordId', 'buck_id', 'mating_date', 'expected_kidding_date',
            'actual_kidding_date', 'notes', 'create_offspring', 'offspring',
        );
        $this->resetValidation();

        if ($breedingRecordId) {
            $breedingRecord = $this->animal->breedingRecordsAsDoe()->findOrFail($breedingRecordId);

            $this->breedingRecordId = $breedingRecord->id;
            $this->buck_id = $breedingRecord->buck_id;
            $this->mating_date = $breedingRecord->mating_date->toDateString();
            $this->expected_kidding_date = $breedingRecord->expected_kidding_date->toDateString();
            $this->actual_kidding_date = $breedingRecord->actual_kidding_date?->toDateString();
            $this->notes = $breedingRecord->notes;
        }

        $this->show = true;
    }

    public function addOffspring(): void
    {
        $this->offspring[] = ['tag_number' => '', 'name' => null, 'sex' => ''];
    }

    public function removeOffspring(int $index): void
    {
        unset($this->offspring[$index]);
        $this->offspring = array_values($this->offspring);
    }

    public function save(CreateOffspringAnimalsAction $createOffspringAnimals): void
    {
        Gate::authorize('update', $this->animal);

        $recordingOffspring = $this->create_offspring && $this->actual_kidding_date && $this->offspring !== [];

        $rules = $this->breedingRecordRules();

        if ($recordingOffspring) {
            $rules = [...$rules, ...$this->offspringRules()];
        }

        $validated = $this->validate($rules);

        $breedingData = Arr::except($validated, 'offspring');

        if ($recordingOffspring) {
            $breedingData['number_of_offspring'] = count($this->offspring);
        }

        if ($this->breedingRecordId) {
            $breedingRecord = $this->animal->breedingRecordsAsDoe()->whereKey($this->breedingRecordId)->firstOrFail();
            $breedingRecord->update($breedingData);
        } else {
            $breedingRecord = $this->animal->breedingRecordsAsDoe()->create($breedingData);
        }

        if ($recordingOffspring) {
            $createOffspringAnimals->handle($breedingRecord, $this->offspring);
        }

        $this->show = false;

        $this->dispatch('breeding-record-saved');
    }

    public function delete(): void
    {
        Gate::authorize('update', $this->animal);

        if ($this->breedingRecordId) {
            $this->animal->breedingRecordsAsDoe()->whereKey($this->breedingRecordId)->delete();
        }

        $this->show = false;

        $this->dispatch('breeding-record-saved');
    }
}
