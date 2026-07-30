<div class="space-y-6">
    <div>
        <flux:heading size="xl">{{ __('Weight Logs') }}</flux:heading>
        <flux:text class="mt-1">{{ __('Track growth across the whole herd.') }}</flux:text>
    </div>

    <div class="flex flex-wrap items-center gap-3">
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
                {{ __('Try adjusting your search or filters, or log a weight from an animal\'s profile.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Animal') }}</flux:table.column>
                <flux:table.column>{{ __('Weight') }}</flux:table.column>
                <flux:table.column>{{ __('Recorded date') }}</flux:table.column>
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
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->weightLogs->links() }}
        </div>
    @endif
</div>
