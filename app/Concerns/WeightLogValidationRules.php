<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait WeightLogValidationRules
{
    /**
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function weightLogRules(): array
    {
        return [
            'weight' => ['required', 'numeric', 'min:0'],
            'recorded_at' => ['required', 'date', 'before_or_equal:today'],
        ];
    }
}
