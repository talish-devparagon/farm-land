<?php

namespace App\Models;

use App\Enums\HealthRecordType;
use App\Models\Concerns\BelongsToFarm;
use Database\Factories\HealthRecordFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['animal_id', 'type', 'description', 'date', 'next_due_date', 'notes'])]
class HealthRecord extends Model
{
    /** @use HasFactory<HealthRecordFactory> */
    use BelongsToFarm, HasFactory;

    protected function casts(): array
    {
        return [
            'type' => HealthRecordType::class,
            'date' => 'date',
            'next_due_date' => 'date',
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
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
