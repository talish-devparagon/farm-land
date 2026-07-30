<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Health Records') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Track vaccinations, treatments, and vet visits across the farm.') }}</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openHealthRecordModal">
            {{ __('New health record') }}
        </flux:button>
    </div>

    <div class="flex flex-wrap items-end gap-3">
        <flux:input
            wire:model.live="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search by tag number or name') }}"
            class="max-w-xs"
        />

        <flux:select wire:model.live="type" placeholder="{{ __('All types') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All types') }}</flux:select.option>
            @foreach ($this->types as $type)
                <flux:select.option value="{{ $type->value }}">
                    {{ ucfirst(str_replace('_', ' ', $type->value)) }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="animalId" placeholder="{{ __('All animals') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All animals') }}</flux:select.option>
            @foreach ($this->animalOptions as $animal)
                <flux:select.option value="{{ $animal->id }}">
                    {{ $animal->tag_number }}{{ $animal->name ? ' · '.$animal->name : '' }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model.live="dateFrom" type="date" :label="__('From')" class="max-w-xs" />
        <flux:input wire:model.live="dateTo" type="date" :label="__('To')" class="max-w-xs" />
    </div>

    @if ($this->healthRecords->isEmpty())
        <flux:callout icon="heart" heading="{{ __('No health records found') }}">
            <flux:callout.text>
                {{ __('Try adjusting your search or filters, or add a new health record to get started.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Animal') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Description') }}</flux:table.column>
                <flux:table.column>{{ __('Date') }}</flux:table.column>
                <flux:table.column>{{ __('Next due date') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->healthRecords as $record)
                    <flux:table.row :key="'health-record-'.$record->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('animals.show', $record->animal) }}" wire:navigate>
                                {{ $record->animal->tag_number }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-health-record-type-badge :type="$record->type" />
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs truncate" title="{{ $record->description }}">
                            {{ $record->description }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $record->date->toFormattedDateString() }}</flux:table.cell>
                        <flux:table.cell>{{ $record->next_due_date?->toFormattedDateString() ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="pencil"
                                    class="hover:text-cyan-600!"
                                    tooltip="{{ __('Edit health record') }}"
                                    aria-label="{{ __('Edit health record') }}"
                                    wire:click="openHealthRecordModal({{ $record->animal_id }}, {{ $record->id }})"
                                />
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="trash"
                                    iconVariant="outline"
                                    class="text-red-500!"
                                    tooltip="{{ __('Delete health record') }}"
                                    aria-label="{{ __('Delete health record') }}"
                                    wire:click="deleteHealthRecord({{ $record->id }})"
                                    wire:confirm="{{ __('Are you sure?') }}"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->healthRecords->links() }}
        </div>
    @endif

    <livewire:animals.health-record-form-modal />
</div>
