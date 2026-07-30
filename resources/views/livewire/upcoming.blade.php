<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Upcoming') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Health and breeding reminders due in the next 30 days.') }}</flux:text>
    </div>

    <div class="space-y-3">
        <flux:heading size="lg">{{ __('Health reminders') }}</flux:heading>

        @if ($this->upcomingHealthRecords->isEmpty())
            <flux:callout icon="check-circle" heading="{{ __('All caught up') }}">
                <flux:callout.text>{{ __('Nothing due in the next 30 days.') }}</flux:callout.text>
            </flux:callout>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Animal') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Due date') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->upcomingHealthRecords as $healthRecord)
                        <flux:table.row :key="'health-'.$healthRecord->id">
                            <flux:table.cell variant="strong">
                                <flux:link href="{{ route('animals.show', $healthRecord->animal) }}" wire:navigate>
                                    {{ $healthRecord->animal->tag_number }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-health-record-type-badge :type="$healthRecord->type" />
                            </flux:table.cell>
                            <flux:table.cell>{{ $healthRecord->next_due_date->toFormattedDateString() }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    <flux:separator />

    <div class="space-y-3">
        <flux:heading size="lg">{{ __('Breeding reminders') }}</flux:heading>

        @if ($this->upcomingBreedingRecords->isEmpty())
            <flux:callout icon="check-circle" heading="{{ __('All caught up') }}">
                <flux:callout.text>{{ __('Nothing due in the next 30 days.') }}</flux:callout.text>
            </flux:callout>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Animal') }}</flux:table.column>
                    <flux:table.column>{{ __('Due date') }}</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->upcomingBreedingRecords as $breedingRecord)
                        <flux:table.row :key="'breeding-'.$breedingRecord->id">
                            <flux:table.cell variant="strong">
                                <flux:link href="{{ route('animals.show', $breedingRecord->doe) }}" wire:navigate>
                                    {{ $breedingRecord->doe->tag_number }}
                                </flux:link>
                            </flux:table.cell>
                            <flux:table.cell>{{ $breedingRecord->expected_kidding_date->toFormattedDateString() }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </div>
</div>
