<div class="max-w-3xl space-y-6">
    <flux:heading size="xl">
        {{ $animal ? __('Edit animal') : __('Add animal') }}
    </flux:heading>

    <flux:text class="mt-1">
        {{ $animal ? __('Update this animal\'s details.') : __('Register a new animal on the farm.') }}
    </flux:text>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <flux:field>
                <flux:label>{{ __('Tag number') }}</flux:label>
                <flux:input wire:model="tag_number" placeholder="{{ __('e.g. TAG-0042') }}" />
                <flux:error name="tag_number" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input wire:model="name" placeholder="{{ __('Optional') }}" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Breed') }}</flux:label>
                <flux:input wire:model="breed" placeholder="{{ __('Optional') }}" />
                <flux:error name="breed" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Sex') }}</flux:label>
                <flux:select wire:model="sex" placeholder="{{ __('Select sex') }}">
                    @foreach (\App\Enums\AnimalSex::cases() as $case)
                        <flux:select.option value="{{ $case->value }}">{{ ucfirst($case->value) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="sex" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Date of birth') }}</flux:label>
                <flux:input type="date" wire:model="date_of_birth" />
                <flux:error name="date_of_birth" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:select wire:model="status" placeholder="{{ __('Select status') }}">
                    @foreach (\App\Enums\AnimalStatus::cases() as $case)
                        <flux:select.option value="{{ $case->value }}">{{ ucfirst($case->value) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="status" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Mother') }}</flux:label>
                <flux:select wire:model="mother_id" placeholder="{{ __('None') }}">
                    <flux:select.option value="">{{ __('None') }}</flux:select.option>
                    @foreach ($this->candidateParents as $candidate)
                        <flux:select.option value="{{ $candidate->id }}">
                            {{ $candidate->tag_number }}{{ $candidate->name ? " ({$candidate->name})" : '' }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="mother_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Father') }}</flux:label>
                <flux:select wire:model="father_id" placeholder="{{ __('None') }}">
                    <flux:select.option value="">{{ __('None') }}</flux:select.option>
                    @foreach ($this->candidateParents as $candidate)
                        <flux:select.option value="{{ $candidate->id }}">
                            {{ $candidate->tag_number }}{{ $candidate->name ? " ({$candidate->name})" : '' }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="father_id" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Current weight') }}</flux:label>
                <flux:input wire:model="current_weight" type="text" placeholder="{{ __('Optional') }}" />
                <flux:error name="current_weight" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>{{ __('Notes') }}</flux:label>
            <flux:textarea wire:model="notes" placeholder="{{ __('Optional notes about this animal') }}" rows="4" />
            <flux:error name="notes" />
        </flux:field>

        <div class="flex items-center gap-4">
            <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
        </div>
    </form>
</div>
