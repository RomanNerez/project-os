<?php

namespace App\Containers\AppSection\Finance\Policies;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

final class FinancePolicy extends ParentPolicy
{
    /**
     * Only the author edits a record; project members only see it.
     *
     * @param User $user
     * @param Finance $finance
     * @return bool
     */
    public function update(User $user, Finance $finance): bool
    {
        return $finance->user_id === $user->id;
    }
}
