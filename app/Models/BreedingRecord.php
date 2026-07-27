<?php

namespace App\Models;

use App\Models\Concerns\BelongsToFarm;
use Database\Factories\BreedingRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'doe_id', 'buck_id', 'mating_date', 'expected_kidding_date',
    'actual_kidding_date', 'number_of_offspring', 'notes',
])]
class BreedingRecord extends Model
{
    /** @use HasFactory<BreedingRecordFactory> */
    use BelongsToFarm, HasFactory;

    protected function casts(): array
    {
        return [
            'mating_date' => 'date',
            'expected_kidding_date' => 'date',
            'actual_kidding_date' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Farm, $this>
     */
    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    /**
     * @return BelongsTo<Animal, $this>
     */
    public function doe(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'doe_id');
    }

    /**
     * @return BelongsTo<Animal, $this>
     */
    public function buck(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'buck_id');
    }
}
