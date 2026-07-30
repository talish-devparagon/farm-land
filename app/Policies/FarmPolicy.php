<?php

namespace App\Policies;

use App\Models\Farm;
use App\Models\User;

class FarmPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Farm $farm): bool
    {
        return $user->isAdmin() || ($user->isFarmOwner() && $farm->owner_id === $user->id);
    }
}
