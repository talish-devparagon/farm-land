@props(['type'])

@php
    $value = $type instanceof \App\Enums\HealthRecordType ? $type->value : $type;

    $color = match ($value) {
        \App\Enums\HealthRecordType::Vaccination->value => 'blue',
        \App\Enums\HealthRecordType::Treatment->value => 'amber',
        \App\Enums\HealthRecordType::VetVisit->value => 'violet',
        default => 'zinc',
    };
@endphp

<flux:badge {{ $attributes }} :color="$color" size="sm" class="capitalize">
    {{ str_replace('_', ' ', $value) }}
</flux:badge>
