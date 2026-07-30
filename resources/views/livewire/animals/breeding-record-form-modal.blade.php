<div>
    <flux:modal wire:model.self="show" class="md:w-full md:max-w-lg">
        <form wire:submit="save" class="space-y-6">
            <div>
                <flux:heading size="lg">
                    {{ $breedingRecordId ? __('Edit breeding record') : __('Add breeding record') }}
                </flux:heading>
            </div>

            <flux:select
                wire:model="animalId"
                :label="__('Doe')"
                placeholder="{{ __('Choose a doe') }}"
                :disabled="$animalLocked || $breedingRecordId"
            >
                @foreach ($this->doeOptions as $option)
                    <flux:select.option value="{{ $option->id }}">
                        {{ $option->tag_number }}{{ $option->name ? ' · '.$option->name : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model="buck_id" :label="__('Buck')" placeholder="{{ __('None') }}">
                @foreach ($this->buckOptions as $buck)
                    <flux:select.option value="{{ $buck->id }}">{{ $buck->tag_number }}</flux:select.option>
                @endforeach
            </flux:select>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <flux:input wire:model="mating_date" type="date" :label="__('Mating date')" />
                <flux:input wire:model="expected_kidding_date" type="date" :label="__('Expected kidding date')" />
            </div>

            <flux:input wire:model.live="actual_kidding_date" type="date" :label="__('Actual kidding date')" />

            <flux:textarea wire:model="notes" :label="__('Notes')" />

            @if ($actual_kidding_date)
                <flux:checkbox wire:model.live="create_offspring" :label="__('Record offspring')" />

                @if ($create_offspring)
                    <div class="space-y-4 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                        @foreach ($offspring as $index => $child)
                            <div wire:key="offspring-{{ $index }}" class="grid grid-cols-1 gap-3 sm:grid-cols-[1fr_1fr_1fr_auto] sm:items-end">
                                <flux:input wire:model="offspring.{{ $index }}.tag_number" :label="__('Tag number')" />
                                <flux:input wire:model="offspring.{{ $index }}.name" :label="__('Name')" />
                                <flux:select wire:model="offspring.{{ $index }}.sex" :label="__('Sex')" placeholder="{{ __('Select sex') }}">
                                    @foreach (\App\Enums\AnimalSex::cases() as $case)
                                        <flux:select.option value="{{ $case->value }}">{{ ucfirst($case->value) }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:button
                                    type="button"
                                    variant="danger"
                                    icon="x-mark"
                                    square
                                    aria-label="{{ __('Remove') }}"
                                    wire:click="removeOffspring({{ $index }})"
                                />
                            </div>
                        @endforeach

                        <flux:button type="button" variant="filled" icon="plus" wire:click="addOffspring">
                            {{ __('Add offspring') }}
                        </flux:button>
                    </div>
                @endif
            @endif

            <div class="flex items-center justify-between gap-2">
                <div>
                    @if ($breedingRecordId)
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
