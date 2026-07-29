@props(['status'])

@php
    $value = $status instanceof \App\Enums\AnimalStatus ? $status->value : $status;

    $color = match ($value) {
        \App\Enums\AnimalStatus::Alive->value => 'green',
        \App\Enums\AnimalStatus::Sold->value => 'blue',
        \App\Enums\AnimalStatus::Deceased->value => 'zinc',
        default => 'zinc',
    };
@endphp

<flux:badge {{ $attributes }} :color="$color" size="sm">
    {{ ucfirst($value) }}
</flux:badge>
