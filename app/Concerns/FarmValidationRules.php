<?php

namespace App\Concerns;

use Illuminate\Contracts\Validation\ValidationRule;

trait FarmValidationRules
{
    /**
     * Get the validation rules used to validate the farm profile.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function farmRules(): array
    {
        return [
            'name' => $this->farmNameRules(),
            'location' => $this->farmLocationRules(),
            'phone' => $this->farmPhoneRules(),
        ];
    }

    /**
     * Get the validation rules used to validate the farm name.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function farmNameRules(): array
    {
        return ['required', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate the farm location.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function farmLocationRules(): array
    {
        return ['nullable', 'string', 'max:255'];
    }

    /**
     * Get the validation rules used to validate the farm phone number.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function farmPhoneRules(): array
    {
        return ['nullable', 'string', 'max:255'];
    }
}
