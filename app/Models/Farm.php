<?php

namespace App\Models;

use App\Enums\FarmStatus;
use Database\Factories\FarmFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'location', 'phone', 'status'])]
class Farm extends Model
{
    /** @use HasFactory<FarmFactory> */
    use HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => FarmStatus::Active,
    ];

    protected function casts(): array
    {
        return [
            'status' => FarmStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * @return HasMany<Animal, $this>
     */
    public function animals(): HasMany
    {
        return $this->hasMany(Animal::class);
    }
}
