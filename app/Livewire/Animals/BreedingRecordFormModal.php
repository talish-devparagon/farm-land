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

    public Animal $animal;

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

    #[On('open-breeding-record-modal')]
    public function open(?int $id = null): void
    {
        Gate::authorize('update', $this->animal);

        $this->reset(
            'breedingRecordId', 'buck_id', 'mating_date', 'expected_kidding_date',
            'actual_kidding_date', 'notes', 'create_offspring', 'offspring',
        );
        $this->resetValidation();

        if ($id) {
            $breedingRecord = $this->animal->breedingRecordsAsDoe()->findOrFail($id);

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
