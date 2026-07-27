<div>
    <div>
        <h2>{{ __('Upcoming health reminders') }}</h2>

        @forelse ($this->upcomingHealthRecords as $healthRecord)
            <div wire:key="health-{{ $healthRecord->id }}">
                <a href="{{ route('animals.show', $healthRecord->animal) }}" wire:navigate>
                    {{ $healthRecord->animal->tag_number }}
                </a>
                — {{ $healthRecord->type->value }} — {{ $healthRecord->next_due_date->toDateString() }}
            </div>
        @empty
            <p>{{ __('Nothing due in the next 30 days.') }}</p>
        @endforelse
    </div>

    <div>
        <h2>{{ __('Upcoming breeding reminders') }}</h2>

        @forelse ($this->upcomingBreedingRecords as $breedingRecord)
            <div wire:key="breeding-{{ $breedingRecord->id }}">
                <a href="{{ route('animals.show', $breedingRecord->doe) }}" wire:navigate>
                    {{ $breedingRecord->doe->tag_number }}
                </a>
                — {{ $breedingRecord->expected_kidding_date->toDateString() }}
            </div>
        @empty
            <p>{{ __('Nothing due in the next 30 days.') }}</p>
        @endforelse
    </div>
</div>
