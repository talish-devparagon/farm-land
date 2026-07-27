<div>
    <div>
        <input type="text" wire:model.live="search" placeholder="{{ __('Search') }}">

        <select wire:model.live="breed">
            <option value="">{{ __('All breeds') }}</option>
            @foreach ($this->breeds as $breed)
                <option value="{{ $breed }}">{{ $breed }}</option>
            @endforeach
        </select>

        <select wire:model.live="status">
            <option value="">{{ __('All statuses') }}</option>
            @foreach ($this->statuses as $status)
                <option value="{{ $status->value }}">{{ $status->value }}</option>
            @endforeach
        </select>

        <a href="{{ route('animals.create') }}" wire:navigate>{{ __('Add animal') }}</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Tag number') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->animals as $animal)
                <tr wire:key="animal-{{ $animal->id }}">
                    <td>
                        <a href="{{ route('animals.show', $animal) }}" wire:navigate>{{ $animal->tag_number }}</a>
                    </td>
                    <td>{{ $animal->name }}</td>
                    <td>{{ $animal->status->value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $this->animals->links() }}
</div>
