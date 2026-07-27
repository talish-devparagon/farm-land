<?php

namespace App\Concerns;

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Animal;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait AnimalValidationRules
{
    /**
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function animalRules(?int $animalId = null): array
    {
        $farmId = Auth::user()?->farm?->id;

        return [
            'tag_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Animal::class)
                    ->where('farm_id', $farmId)
                    ->ignore($animalId),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'breed' => ['nullable', 'string', 'max:255'],
            'sex' => ['required', new Enum(AnimalSex::class)],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'mother_id' => [
                'nullable',
                Rule::exists(Animal::class, 'id')->where('farm_id', $farmId),
                Rule::notIn($animalId ? [$animalId] : []),
            ],
            'father_id' => [
                'nullable',
                Rule::exists(Animal::class, 'id')->where('farm_id', $farmId),
                Rule::notIn($animalId ? [$animalId] : []),
            ],
            'status' => ['required', new Enum(AnimalStatus::class)],
            'current_weight' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
