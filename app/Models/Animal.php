<?php

namespace App\Models;

use App\Enums\AnimalSex;
use App\Enums\AnimalStatus;
use App\Models\Concerns\BelongsToFarm;
use Database\Factories\AnimalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'tag_number', 'name', 'breed', 'sex', 'date_of_birth',
    'mother_id', 'father_id', 'status', 'current_weight', 'notes',
])]
class Animal extends Model
{
    /** @use HasFactory<AnimalFactory> */
    use BelongsToFarm, HasFactory, SoftDeletes;

    protected $attributes = [
        'status' => AnimalStatus::Alive,
    ];

    protected function casts(): array
    {
        return [
            'sex' => AnimalSex::class,
            'status' => AnimalStatus::class,
            'date_of_birth' => 'date',
            'current_weight' => 'decimal:2',
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
    public function mother(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'mother_id');
    }

    /**
     * @return BelongsTo<Animal, $this>
     */
    public function father(): BelongsTo
    {
        return $this->belongsTo(Animal::class, 'father_id');
    }

    /**
     * @return HasMany<Animal, $this>
     */
    public function offspringAsMother(): HasMany
    {
        return $this->hasMany(Animal::class, 'mother_id');
    }

    /**
     * @return HasMany<Animal, $this>
     */
    public function offspringAsFather(): HasMany
    {
        return $this->hasMany(Animal::class, 'father_id');
    }

    /**
     * @return HasMany<WeightLog, $this>
     */
    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class);
    }

    /**
     * @return HasMany<HealthRecord, $this>
     */
    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    /**
     * @return HasMany<BreedingRecord, $this>
     */
    public function breedingRecordsAsDoe(): HasMany
    {
        return $this->hasMany(BreedingRecord::class, 'doe_id');
    }

    /**
     * @return HasMany<BreedingRecord, $this>
     */
    public function breedingRecordsAsBuck(): HasMany
    {
        return $this->hasMany(BreedingRecord::class, 'buck_id');
    }

    /**
     * All recorded offspring of this animal, regardless of whether it was the mother or father.
     *
     * @return Collection<int, Animal>
     */
    public function offspring(): Collection
    {
        return $this->offspringAsMother
            ->concat($this->offspringAsFather)
            ->sortByDesc('date_of_birth')
            ->values();
    }
}
