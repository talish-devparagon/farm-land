<div class="space-y-6" x-data="{ newRecordAnimalId: '' }">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Weight Logs') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Track growth across the whole herd.') }}</flux:text>
        </div>

        <div class="flex flex-wrap items-end gap-2">
            <flux:select x-model="newRecordAnimalId" placeholder="{{ __('Choose an animal') }}" class="max-w-44">
                @foreach ($this->animalOptions as $animal)
                    <flux:select.option value="{{ $animal->id }}">
                        {{ $animal->tag_number }}{{ $animal->name ? ' · '.$animal->name : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:button
                variant="primary"
                icon="plus"
                x-on:click="newRecordAnimalId && $wire.openWeightLogModal(newRecordAnimalId)"
                x-bind:disabled="!newRecordAnimalId"
            >
                {{ __('Log weight') }}
            </flux:button>
        </div>
    </div>

    <div class="flex flex-wrap items-end gap-3">
        <flux:input
            wire:model.live="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search by tag number or name') }}"
            class="max-w-xs"
        />

        <flux:select wire:model.live="animalId" placeholder="{{ __('All animals') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All animals') }}</flux:select.option>
            @foreach ($this->animalOptions as $animal)
                <flux:select.option value="{{ $animal->id }}">{{ $animal->tag_number }}{{ $animal->name ? ' — '.$animal->name : '' }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model.live="dateFrom" type="date" :label="__('From')" class="max-w-xs" />
        <flux:input wire:model.live="dateTo" type="date" :label="__('To')" class="max-w-xs" />
    </div>

    @if ($this->weightLogs->isEmpty())
        <flux:callout icon="scale" heading="{{ __('No weight logs found') }}">
            <flux:callout.text>
                {{ __('Try adjusting your search or filters, or pick an animal above to log a new weight entry.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Animal') }}</flux:table.column>
                <flux:table.column>{{ __('Weight') }}</flux:table.column>
                <flux:table.column>{{ __('Recorded date') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->weightLogs as $log)
                    <flux:table.row :key="'weight-log-'.$log->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('animals.show', $log->animal) }}" wire:navigate>
                                {{ $log->animal->tag_number }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ __(':weight kg', ['weight' => $log->weight]) }}</flux:table.cell>
                        <flux:table.cell>{{ $log->recorded_at->toFormattedDateString() }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="pencil"
                                    class="hover:text-cyan-600!"
                                    tooltip="{{ __('Edit weight log') }}"
                                    aria-label="{{ __('Edit weight log') }}"
                                    wire:click="openWeightLogModal({{ $log->animal_id }}, {{ $log->id }})"
                                />
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="trash"
                                    iconVariant="outline"
                                    class="text-red-500!"
                                    tooltip="{{ __('Delete weight log') }}"
                                    aria-label="{{ __('Delete weight log') }}"
                                    wire:click="deleteWeightLog({{ $log->id }})"
                                    wire:confirm="{{ __('Are you sure?') }}"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->weightLogs->links() }}
        </div>
    @endif

    <livewire:weight-logs.weight-log-form-modal />
</div>
