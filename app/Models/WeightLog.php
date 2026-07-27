<?php

namespace App\Models;

use Database\Factories\WeightLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['weight', 'recorded_at'])]
class WeightLog extends Model
{
    /** @use HasFactory<WeightLogFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'recorded_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<Animal, $this>
     */
    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }
}
