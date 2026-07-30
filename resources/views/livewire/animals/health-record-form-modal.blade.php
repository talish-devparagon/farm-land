<div>
    <flux:modal wire:model.self="show" class="md:w-full md:max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $healthRecordId ? __('Edit health record') : __('Add health record') }}
                </flux:heading>
            </div>

            <flux:select
                wire:model="animalId"
                :label="__('Animal')"
                placeholder="{{ __('Choose an animal') }}"
                :disabled="$animalLocked || $healthRecordId"
            >
                @foreach ($this->animalOptions as $option)
                    <flux:select.option value="{{ $option->id }}">
                        {{ $option->tag_number }}{{ $option->name ? ' · '.$option->name : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="type" :label="__('Type')" placeholder="{{ __('Select type') }}">
                @foreach (\App\Enums\HealthRecordType::cases() as $case)
                    <flux:select.option value="{{ $case->value }}">{{ ucfirst(str_replace('_', ' ', $case->value)) }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="description" :label="__('Description')" />

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input wire:model="date" type="date" :label="__('Date')" />
                <flux:input wire:model="next_due_date" type="date" :label="__('Next due date')" />
            </div>

            <flux:textarea wire:model="notes" :label="__('Notes')" />

            <div class="flex items-center justify-between gap-2">
                <div>
                    @if ($healthRecordId)
                        <flux:button variant="danger" wire:click="delete" wire:confirm="{{ __('Are you sure?') }}">
                            {{ __('Delete') }}
                        </flux:button>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>
</div>
