<div>
    <div x-show="@js($show)">
        <form wire:submit="save">
            <select wire:model="type">
                <option value="">{{ __('Select type') }}</option>
                @foreach (\App\Enums\HealthRecordType::cases() as $case)
                    <option value="{{ $case->value }}">{{ $case->value }}</option>
                @endforeach
            </select>

            <textarea wire:model="description" placeholder="{{ __('Description') }}"></textarea>

            <input type="date" wire:model="date">
            <input type="date" wire:model="next_due_date">
            <textarea wire:model="notes" placeholder="{{ __('Notes') }}"></textarea>

            <button type="submit">{{ __('Save') }}</button>

            @if ($healthRecordId)
                <button type="button" wire:click="delete" wire:confirm="{{ __('Are you sure?') }}">{{ __('Delete') }}</button>
            @endif
        </form>
    </div>
</div>
