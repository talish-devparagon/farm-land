<div class="space-y-6">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <flux:heading size="xl">{{ __('Animals') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Browse and manage every animal on the farm.') }}</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" href="{{ route('animals.create') }}" wire:navigate>
            {{ __('Add animal') }}
        </flux:button>
    </div>

    <div class="flex flex-wrap items-end gap-3">
        <flux:input
            wire:model.live="search"
            icon="magnifying-glass"
            placeholder="{{ __('Search by tag number or name') }}"
            class="max-w-xs"
        />

        <flux:select wire:model.live="breed" placeholder="{{ __('All breeds') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All breeds') }}</flux:select.option>
            @foreach ($this->breeds as $breed)
                <flux:select.option value="{{ $breed }}">{{ $breed }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="status" placeholder="{{ __('All statuses') }}" class="max-w-xs">
            <flux:select.option value="">{{ __('All statuses') }}</flux:select.option>
            @foreach ($this->statuses as $status)
                <flux:select.option value="{{ $status->value }}">{{ ucfirst($status->value) }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    @if ($this->animals->isEmpty())
        <flux:callout icon="rectangle-stack" heading="{{ __('No animals found') }}">
            <flux:callout.text>
                {{ __('Try adjusting your search or filters, or add a new animal to get started.') }}
            </flux:callout.text>
        </flux:callout>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Tag number') }}</flux:table.column>
                <flux:table.column>{{ __('Name') }}</flux:table.column>
                <flux:table.column>{{ __('Breed') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($this->animals as $animal)
                    <flux:table.row :key="'animal-'.$animal->id">
                        <flux:table.cell variant="strong">
                            <flux:link href="{{ route('animals.show', $animal) }}" wire:navigate>
                                {{ $animal->tag_number }}
                            </flux:link>
                        </flux:table.cell>
                        <flux:table.cell>{{ $animal->name ?? '—' }}</flux:table.cell>
                        <flux:table.cell>{{ $animal->breed ?? '—' }}</flux:table.cell>
                        <flux:table.cell>
                            <x-animal-status-badge :status="$animal->status" />
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-1">
                                <flux:button
                                    size="sm"
                                    variant="subtle"
                                    icon="pencil"
                                    tooltip="{{ __('Edit animal') }}"
                                    aria-label="{{ __('Edit animal') }}"
                                    href="{{ route('animals.edit', $animal) }}"
                                    wire:navigate
                                />
                                <flux:button
                                    size="sm"
                                    variant="danger"
                                    icon="trash"
                                    tooltip="{{ __('Delete animal') }}"
                                    aria-label="{{ __('Delete animal') }}"
                                    wire:click="delete({{ $animal->id }})"
                                    wire:confirm="{{ __('Are you sure?') }}"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        <div class="mt-4">
            {{ $this->animals->links() }}
        </div>
    @endif
</div>
