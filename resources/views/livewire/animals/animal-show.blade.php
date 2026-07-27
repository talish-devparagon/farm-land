<div>
    <div>
        <h1>{{ $animal->tag_number }}</h1>
        <p>{{ $animal->name }}</p>
        <p>{{ $animal->status->value }}</p>

        <a href="{{ route('animals.edit', $animal) }}" wire:navigate>{{ __('Edit') }}</a>
        <button type="button" wire:click="delete" wire:confirm="{{ __('Are you sure?') }}">{{ __('Delete') }}</button>
    </div>

    <div>
        <button type="button" wire:click="$set('activeTab', 'health')">{{ __('Health') }}</button>
        <button type="button" wire:click="$set('activeTab', 'breeding')">{{ __('Breeding') }}</button>
        <button type="button" wire:click="$set('activeTab', 'offspring')">{{ __('Offspring') }}</button>
    </div>

    @if ($activeTab === 'health')
        <div>
            <button type="button" wire:click="openHealthRecordModal">{{ __('Add health record') }}</button>
            <ul>
                @foreach ($this->healthRecords as $healthRecord)
                    <li wire:key="health-{{ $healthRecord->id }}">
                        {{ $healthRecord->type->value }} — {{ $healthRecord->date->toDateString() }}
                        <button type="button" wire:click="openHealthRecordModal({{ $healthRecord->id }})">{{ __('Edit') }}</button>
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif ($activeTab === 'breeding')
        <div>
            <button type="button" wire:click="openBreedingRecordModal">{{ __('Add breeding record') }}</button>
            <ul>
                @foreach ($this->breedingRecords as $breedingRecord)
                    <li wire:key="breeding-{{ $breedingRecord->id }}">
                        {{ $breedingRecord->mating_date->toDateString() }}
                        <button type="button" wire:click="openBreedingRecordModal({{ $breedingRecord->id }})">{{ __('Edit') }}</button>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div>
            <ul>
                @foreach ($this->offspring as $child)
                    <li wire:key="offspring-{{ $child->id }}">{{ $child->tag_number }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Weight') }}</th>
                    <th>{{ __('Recorded at') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->weightLogs as $weightLog)
                    <tr wire:key="weight-{{ $weightLog->id }}">
                        <td>{{ $weightLog->weight }}</td>
                        <td>{{ $weightLog->recorded_at->toDateString() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form wire:submit="logWeight">
            <input type="text" wire:model="weight" placeholder="{{ __('Weight') }}">
            <input type="date" wire:model="recorded_at">
            <button type="submit">{{ __('Log weight') }}</button>
        </form>
    </div>

    <livewire:animals.health-record-form-modal :animal="$animal" />
    <livewire:animals.breeding-record-form-modal :animal="$animal" />
</div>
