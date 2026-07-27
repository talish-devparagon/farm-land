<div>
    <form wire:submit="save">
        <input type="text" wire:model="tag_number" placeholder="{{ __('Tag number') }}">
        <input type="text" wire:model="name" placeholder="{{ __('Name') }}">
        <input type="text" wire:model="breed" placeholder="{{ __('Breed') }}">

        <select wire:model="sex">
            <option value="">{{ __('Select sex') }}</option>
            @foreach (\App\Enums\AnimalSex::cases() as $case)
                <option value="{{ $case->value }}">{{ $case->value }}</option>
            @endforeach
        </select>

        <input type="date" wire:model="date_of_birth">

        <select wire:model="mother_id">
            <option value="">{{ __('None') }}</option>
            @foreach ($this->candidateParents as $candidate)
                <option value="{{ $candidate->id }}">{{ $candidate->tag_number }}</option>
            @endforeach
        </select>

        <select wire:model="father_id">
            <option value="">{{ __('None') }}</option>
            @foreach ($this->candidateParents as $candidate)
                <option value="{{ $candidate->id }}">{{ $candidate->tag_number }}</option>
            @endforeach
        </select>

        <select wire:model="status">
            @foreach (\App\Enums\AnimalStatus::cases() as $case)
                <option value="{{ $case->value }}">{{ $case->value }}</option>
            @endforeach
        </select>

        <input type="text" wire:model="current_weight" placeholder="{{ __('Current weight') }}">
        <textarea wire:model="notes" placeholder="{{ __('Notes') }}"></textarea>

        <button type="submit">{{ __('Save') }}</button>
    </form>
</div>
