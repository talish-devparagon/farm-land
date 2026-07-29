<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="space-y-1">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl">{{ $animal->tag_number }}</flux:heading>
                <x-animal-status-badge :status="$animal->status" />
            </div>
            <flux:text size="lg" class="text-zinc-500 dark:text-zinc-400">
                {{ $animal->name ?: __('Unnamed') }}
            </flux:text>
        </div>

        <div class="flex items-center gap-2">
            <flux:button href="{{ route('animals.edit', $animal) }}" wire:navigate>
                {{ __('Edit') }}
            </flux:button>

            <flux:modal.trigger name="confirm-animal-deletion">
                <flux:button variant="danger">{{ __('Delete') }}</flux:button>
            </flux:modal.trigger>

            <flux:modal name="confirm-animal-deletion" class="max-w-sm">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ __('Delete this animal?') }}</flux:heading>
                        <flux:subheading>
                            {{ __('This will permanently remove :tag and its records. This action cannot be undone.', ['tag' => $animal->tag_number]) }}
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end gap-2">
                        <flux:modal.close>
                            <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>

                        <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    </div>

    <flux:card class="space-y-4">
        <div class="grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-3 lg:grid-cols-4">
            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Breed') }}</flux:text>
                <flux:text>{{ $animal->breed ?: '—' }}</flux:text>
            </div>
            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Sex') }}</flux:text>
                <flux:text class="capitalize">{{ $animal->sex->value }}</flux:text>
            </div>
            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Date of birth') }}</flux:text>
                <flux:text>{{ $animal->date_of_birth?->toFormattedDateString() ?? '—' }}</flux:text>
            </div>
            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Current weight') }}</flux:text>
                <flux:text>{{ $animal->current_weight !== null ? __(':weight kg', ['weight' => $animal->current_weight]) : '—' }}</flux:text>
            </div>
            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Mother') }}</flux:text>
                @if ($this->mother)
                    <flux:link href="{{ route('animals.show', $this->mother) }}" wire:navigate>
                        {{ $this->mother->tag_number }}
                    </flux:link>
                @else
                    <flux:text>—</flux:text>
                @endif
            </div>
            <div>
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Father') }}</flux:text>
                @if ($this->father)
                    <flux:link href="{{ route('animals.show', $this->father) }}" wire:navigate>
                        {{ $this->father->tag_number }}
                    </flux:link>
                @else
                    <flux:text>—</flux:text>
                @endif
            </div>
        </div>

        @if ($animal->notes)
            <div>
                <flux:separator class="mb-4" />
                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ __('Notes') }}</flux:text>
                <flux:text class="whitespace-pre-line">{{ $animal->notes }}</flux:text>
            </div>
        @endif
    </flux:card>

    <div class="space-y-4">
        <flux:button.group>
            <flux:button
                wire:click="$set('activeTab', 'health')"
                :variant="$activeTab === 'health' ? 'primary' : 'filled'"
            >
                {{ __('Health') }}
            </flux:button>
            <flux:button
                wire:click="$set('activeTab', 'breeding')"
                :variant="$activeTab === 'breeding' ? 'primary' : 'filled'"
            >
                {{ __('Breeding') }}
            </flux:button>
            <flux:button
                wire:click="$set('activeTab', 'offspring')"
                :variant="$activeTab === 'offspring' ? 'primary' : 'filled'"
            >
                {{ __('Offspring') }}
            </flux:button>
        </flux:button.group>

        @if ($activeTab === 'health')
            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Health records') }}</flux:heading>
                    <flux:button size="sm" icon="plus" wire:click="openHealthRecordModal">
                        {{ __('Add health record') }}
                    </flux:button>
                </div>

                @if ($this->healthRecords->isEmpty())
                    <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('No health records yet.') }}</flux:text>
                @else
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->healthRecords as $healthRecord)
                            <div wire:key="health-{{ $healthRecord->id }}" class="flex items-center justify-between py-3">
                                <div>
                                    <flux:text class="font-medium">{{ ucfirst(str_replace('_', ' ', $healthRecord->type->value)) }}</flux:text>
                                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                                        {{ $healthRecord->date->toFormattedDateString() }}
                                        @if ($healthRecord->next_due_date)
                                            &middot; {{ __('Next due') }}: {{ $healthRecord->next_due_date->toFormattedDateString() }}
                                        @endif
                                    </flux:text>
                                </div>
                                <flux:button size="sm" variant="filled" wire:click="openHealthRecordModal({{ $healthRecord->id }})">
                                    {{ __('Edit') }}
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        @elseif ($activeTab === 'breeding')
            <flux:card class="space-y-3">
                <div class="flex items-center justify-between">
                    <flux:heading size="lg">{{ __('Breeding records') }}</flux:heading>
                    <flux:button size="sm" icon="plus" wire:click="openBreedingRecordModal">
                        {{ __('Add breeding record') }}
                    </flux:button>
                </div>

                @if ($this->breedingRecords->isEmpty())
                    <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('No breeding records yet.') }}</flux:text>
                @else
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->breedingRecords as $breedingRecord)
                            <div wire:key="breeding-{{ $breedingRecord->id }}" class="flex items-center justify-between py-3">
                                <div>
                                    <flux:text class="font-medium">{{ __('Mated') }}: {{ $breedingRecord->mating_date->toFormattedDateString() }}</flux:text>
                                    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                                        {{ __('Expected kidding') }}: {{ $breedingRecord->expected_kidding_date->toFormattedDateString() }}
                                    </flux:text>
                                </div>
                                <flux:button size="sm" variant="filled" wire:click="openBreedingRecordModal({{ $breedingRecord->id }})">
                                    {{ __('Edit') }}
                                </flux:button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        @else
            <flux:card class="space-y-3">
                <flux:heading size="lg">{{ __('Offspring') }}</flux:heading>

                @if ($this->offspring->isEmpty())
                    <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('No offspring recorded yet.') }}</flux:text>
                @else
                    <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach ($this->offspring as $child)
                            <div wire:key="offspring-{{ $child->id }}" class="flex items-center justify-between py-3">
                                <flux:link href="{{ route('animals.show', $child) }}" wire:navigate>
                                    {{ $child->tag_number }}
                                </flux:link>
                                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $child->name }}</flux:text>
                            </div>
                        @endforeach
                    </div>
                @endif
            </flux:card>
        @endif
    </div>

    <flux:card class="space-y-4">
        <flux:heading size="lg">{{ __('Weight history') }}</flux:heading>

        @if ($this->weightLogs->isNotEmpty())
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Weight') }}</flux:table.column>
                    <flux:table.column>{{ __('Recorded at') }}</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach ($this->weightLogs as $weightLog)
                        <flux:table.row :key="'weight-'.$weightLog->id">
                            <flux:table.cell>{{ __(':weight kg', ['weight' => $weightLog->weight]) }}</flux:table.cell>
                            <flux:table.cell>{{ $weightLog->recorded_at->toFormattedDateString() }}</flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @else
            <flux:text class="text-zinc-500 dark:text-zinc-400">{{ __('No weight logs yet.') }}</flux:text>
        @endif

        <flux:separator />

        <form wire:submit="logWeight" class="flex flex-wrap items-end gap-3">
            <flux:input wire:model="weight" type="text" inputmode="decimal" :label="__('Weight (kg)')" class="w-32" />
            <flux:input wire:model="recorded_at" type="date" :label="__('Recorded at')" class="w-48" />
            <flux:button type="submit" variant="primary">{{ __('Log weight') }}</flux:button>
        </form>
    </flux:card>

    <livewire:animals.health-record-form-modal :animal="$animal" />
    <livewire:animals.breeding-record-form-modal :animal="$animal" />
</div>
