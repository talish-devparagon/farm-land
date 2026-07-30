<?php

namespace App\Livewire\Farm;

use App\Concerns\FarmValidationRules;
use App\Enums\FarmStatus;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Farm Settings')]
class FarmSettings extends Component
{
    use FarmValidationRules;

    public string $name = '';

    public ?string $location = null;

    public ?string $phone = null;

    public string $status = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        Gate::authorize('update', Auth::user()->farm);

        $farm = Auth::user()->farm;

        $this->name = $farm->name;
        $this->location = $farm->location;
        $this->phone = $farm->phone;
        $this->status = $farm->status->value;
    }

    /**
     * Update the farm profile information for the currently authenticated user's farm.
     */
    public function update(): void
    {
        $farm = Auth::user()->farm;

        Gate::authorize('update', $farm);

        $rules = $this->farmRules();

        if ($this->canEditStatus) {
            $rules['status'] = ['required', Rule::enum(FarmStatus::class)];
        }

        $validated = $this->validate($rules);

        if (! $this->canEditStatus) {
            unset($validated['status']);
        }

        $farm->update($validated);

        $this->status = $farm->fresh()->status->value;

        Flux::toast(variant: 'success', text: __('Farm settings updated.'));
    }

    /**
     * Determine whether the current user is allowed to edit the farm status.
     */
    #[Computed]
    public function canEditStatus(): bool
    {
        return Auth::user()->isAdmin();
    }
}
