<?php

namespace App\Livewire\Animals;

use App\Enums\AnimalStatus;
use App\Models\Animal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Animals')]
class AnimalsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $breed = '';

    #[Url]
    public string $status = '';

    public function mount(): void
    {
        Gate::authorize('viewAny', Animal::class);
    }

    /**
     * @return LengthAwarePaginator<int, Animal>
     */
    #[Computed]
    public function animals(): LengthAwarePaginator
    {
        return Animal::query()
            ->when($this->search, fn ($query) => $query->where(function ($query): void {
                $query->where('tag_number', 'like', "%{$this->search}%")
                    ->orWhere('name', 'like', "%{$this->search}%");
            }))
            ->when($this->breed, fn ($query) => $query->where('breed', $this->breed))
            ->when($this->status, fn ($query) => $query->where('status', $this->status))
            ->latest()
            ->paginate(15);
    }

    /**
     * @return Collection<int, string>
     */
    #[Computed]
    public function breeds(): Collection
    {
        return Animal::query()
            ->whereNotNull('breed')
            ->distinct()
            ->orderBy('breed')
            ->pluck('breed');
    }

    /**
     * @return array<int, AnimalStatus>
     */
    #[Computed]
    public function statuses(): array
    {
        return AnimalStatus::cases();
    }

    public function delete(int $animalId): void
    {
        $animal = Animal::findOrFail($animalId);

        Gate::authorize('delete', $animal);

        $animal->delete();
    }
}
