<?php

namespace App\Concerns;

use App\Enums\HealthRecordType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Enum;

trait HealthRecordValidationRules
{
    /**
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function healthRecordRules(): array
    {
        return [
            'type' => ['required', new Enum(HealthRecordType::class)],
            'description' => ['required', 'string'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'next_due_date' => ['nullable', 'date', 'after:date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
