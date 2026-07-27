<?php

namespace App\Concerns;

use App\Enums\AnimalSex;
use App\Models\Animal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait BreedingRecordValidationRules
{
    /**
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function breedingRecordRules(): array
    {
        $farmId = Auth::user()?->farm?->id;

        return [
            'buck_id' => [
                'nullable',
                Rule::exists(Animal::class, 'id')
                    ->where('farm_id', $farmId)
                    ->where('sex', AnimalSex::Male->value),
            ],
            'mating_date' => ['required', 'date', 'before_or_equal:today'],
            'expected_kidding_date' => ['required', 'date', 'after:mating_date'],
            'actual_kidding_date' => ['nullable', 'date', 'after_or_equal:mating_date'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function offspringRules(): array
    {
        $farmId = Auth::user()?->farm?->id;

        return [
            'offspring' => ['array'],
            'offspring.*.tag_number' => [
                'required_with:offspring',
                'string',
                'distinct',
                Rule::unique(Animal::class, 'tag_number')->where('farm_id', $farmId),
            ],
            'offspring.*.name' => ['nullable', 'string', 'max:255'],
            'offspring.*.sex' => ['required_with:offspring', new Enum(AnimalSex::class)],
        ];
    }
}
