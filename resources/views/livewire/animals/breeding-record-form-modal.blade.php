<div>
    <div x-show="@js($show)">
        <form wire:submit="save">
            <select wire:model="buck_id">
                <option value="">{{ __('None') }}</option>
                @foreach (\App\Models\Animal::query()->where('sex', \App\Enums\AnimalSex::Male->value)->get() as $buck)
                    <option value="{{ $buck->id }}">{{ $buck->tag_number }}</option>
                @endforeach
            </select>

            <input type="date" wire:model="mating_date">
            <input type="date" wire:model="expected_kidding_date">
            <input type="date" wire:model="actual_kidding_date">
            <textarea wire:model="notes" placeholder="{{ __('Notes') }}"></textarea>

            @if ($actual_kidding_date)
                <label>
                    <input type="checkbox" wire:model.live="create_offspring">
                    {{ __('Record offspring') }}
                </label>

                @if ($create_offspring)
                    @foreach ($offspring as $index => $child)
                        <div wire:key="offspring-{{ $index }}">
                            <input type="text" wire:model="offspring.{{ $index }}.tag_number" placeholder="{{ __('Tag number') }}">
                            <input type="text" wire:model="offspring.{{ $index }}.name" placeholder="{{ __('Name') }}">
                            <select wire:model="offspring.{{ $index }}.sex">
                                <option value="">{{ __('Select sex') }}</option>
                                @foreach (\App\Enums\AnimalSex::cases() as $case)
                                    <option value="{{ $case->value }}">{{ $case->value }}</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="removeOffspring({{ $index }})">{{ __('Remove') }}</button>
                        </div>
                    @endforeach

                    <button type="button" wire:click="addOffspring">{{ __('Add offspring') }}</button>
                @endif
            @endif

            <button type="submit">{{ __('Save') }}</button>

            @if ($breedingRecordId)
                <button type="button" wire:click="delete" wire:confirm="{{ __('Are you sure?') }}">{{ __('Delete') }}</button>
            @endif
        </form>
    </div>
</div>
