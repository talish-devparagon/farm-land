<?php

namespace App\Livewire\Animals;

use App\Concerns\AnimalValidationRules;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Animal')]
class AnimalForm extends Component
{
    use AnimalValidationRules;

    public ?Animal $animal = null;

    public string $tag_number = '';

    public ?string $name = null;

    public ?string $breed = null;

    public string $sex = '';

    public ?string $date_of_birth = null;

    public ?int $mother_id = null;

    public ?int $father_id = null;

    public string $status = '';

    public ?string $current_weight = null;

    public ?string $notes = null;

    public function mount(?Animal $animal = null): void
    {
        if ($animal) {
            Gate::authorize('update', $animal);

            $this->animal = $animal;
            $this->tag_number = $animal->tag_number;
            $this->name = $animal->name;
            $this->breed = $animal->breed;
            $this->sex = $animal->sex->value;
            $this->date_of_birth = $animal->date_of_birth?->toDateString();
            $this->mother_id = $animal->mother_id;
            $this->father_id = $animal->father_id;
            $this->status = $animal->status->value;
            $this->current_weight = $animal->current_weight === null ? null : (string) $animal->current_weight;
            $this->notes = $animal->notes;

            return;
        }

        Gate::authorize('create', Animal::class);

        $this->status = AnimalStatus::Alive->value;
    }

    public function save(): void
    {
        $validated = $this->validate($this->animalRules($this->animal?->id));

        if ($this->animal) {
            Gate::authorize('update', $this->animal);

            $this->animal->update($validated);
        } else {
            Gate::authorize('create', Animal::class);

            $this->animal = Animal::create($validated);
        }

        $this->redirectRoute('animals.show', $this->animal, navigate: true);
    }

    /**
     * @return Collection<int, Animal>
     */
    #[Computed]
    public function candidateParents(): Collection
    {
        return Animal::query()
            ->when($this->animal, fn ($query) => $query->whereKeyNot($this->animal->id))
            ->orderBy('tag_number')
            ->get();
    }
}
