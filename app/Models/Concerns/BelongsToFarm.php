<?php

namespace App\Models\Concerns;

use App\Models\Scopes\FarmScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToFarm
{
    protected static function bootBelongsToFarm(): void
    {
        static::addGlobalScope(new FarmScope);

        static::creating(function ($model): void {
            if ($model->farm_id) {
                return;
            }

            $user = Auth::user();

            if ($user && ! $user->isAdmin()) {
                $model->farm_id = $user->farm?->id;
            }
        });
    }
}
