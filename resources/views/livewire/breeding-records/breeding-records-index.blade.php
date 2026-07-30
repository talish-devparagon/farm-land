<div class="space-y-6" x-data="{ newDoeId: '' }">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Breeding Records') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Track matings and kidding across the whole farm.') }}</flux:text>
        </div>

        <div class="flex flex-wrap items-end gap-2">
            <flux:select x-model="newDoeId" placeholder="{{ __('Select a doe') }}" class="max-w-44">
                @foreach ($this->doeOptions as $doe)
                    <flux:select.option value="{{ $doe->id }}">
                        {{ $doe->tag_number }}{{ $doe->name ? ' · '.$doe->name : '' }}
                    </flux:select.option>
                @endforeach
            </flux:select>

            <flux:button
                variant="primary"
                icon="plus"
                x-bind:disabled="!newDoeId"
                x-on:click="$wire.openBreedingRecordModal(Number(newDoeId))"
            >
                {{ __('New breeding record') }}
            </flux:button>
        </div>
    </div>

    <div class="flex flex-wrap items-end gap-3">
        <flux:input
            wire:model.live="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search by doe or buck tag') }}"
            class="max-w-xs"
        />

        <flux:select wire:model.live="doeId" placeholder="{{ __('All does') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All does') }}</flux:select.option>
            @foreach ($this->doeOptions as $doe)
                <flux:select.option value="{{ $doe->id }}">{{ $doe->tag_number }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="status" placeholder="{{ __('All statuses') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($this->statuses as $value => $label)
                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:input wire:model.live="dateFrom" type="date" :label="__('Mated from')" class="max-w-40" />
        <flux:input wire:model.live="dateTo" type="date" :label="__('Mated to')" class="max-w-40" />
    </div>

    @if ($this->breedingRecords->isEmpty())
        <flux:callout icon="heart" heading="{{ __('No breeding records found') }}">
            <flux:callout.text>
                {{ __('Try adjusting your search or filters, or pick a doe above to log a new breeding record.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Doe') }}</flux:table.column>
                <flux:table.column>{{ __('Buck') }}</flux:table.column>
                <flux:table.column>{{ __('Mating date') }}</flux:table.column>
                <flux:table.column>{{ __('Expected kidding') }}</flux:table.column>
                <flux:table.column>{{ __('Actual kidding') }}</flux:table.column>
                <flux:table.column>{{ __('Offspring') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->breedingRecords as $breedingRecord)
                    <flux:table.row :key="'breeding-record-'.$breedingRecord->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('animals.show', $breedingRecord->doe) }}" wire:navigate>
                                {{ $breedingRecord->doe->tag_number }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($breedingRecord->buck)
                                <flux:link href="{{ route('animals.show', $breedingRecord->buck) }}" wire:navigate>
                                    {{ $breedingRecord->buck->tag_number }}
                                </flux:link>
                            @else
                                —
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $breedingRecord->mating_date->toFormattedDateString() }}</flux:table.cell>
                        <flux:table.cell>{{ $breedingRecord->expected_kidding_date->toFormattedDateString() }}</flux:table.cell>
                        <flux:table.cell>
                            {{ $breedingRecord->actual_kidding_date?->toFormattedDateString() ?? '—' }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $breedingRecord->number_of_offspring ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($breedingRecord->actual_kidding_date)
                                <flux:badge color="green" size="sm">{{ __('Complete') }}</flux:badge>
                            @else
                                <flux:badge color="amber" size="sm">{{ __('Pending') }}</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                size="sm"
                                variant="subtle"
                                icon="pencil"
                                tooltip="{{ __('Edit breeding record') }}"
                                aria-label="{{ __('Edit breeding record') }}"
                                wire:click="openBreedingRecordModal({{ $breedingRecord->doe_id }}, {{ $breedingRecord->id }})"
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->breedingRecords->links() }}
        </div>
    @endif

    <livewire:animals.breeding-record-form-modal />
</div>
