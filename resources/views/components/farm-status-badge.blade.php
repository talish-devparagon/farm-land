@props(['status'])

@php
    $value = $status instanceof \App\Enums\FarmStatus ? $status->value : $status;

    $color = match ($value) {
        \App\Enums\FarmStatus::Active->value => 'green',
        \App\Enums\FarmStatus::Suspended->value => 'red',
        default => 'zinc',
    };
@endphp

<flux:badge {{ $attributes }} :color="$color" size="sm">
    {{ __(ucfirst($value)) }}
</flux:badge>
