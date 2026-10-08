<?php

namespace App\Containers\AppSection\Project\Policies;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Policies\Policy as ParentPolicy;

abstract class BasePolicy extends ParentPolicy
{
    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    protected function isOwnerOrAdmin(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user) || ProjectRole::ADMIN === $project->memberRole($user);
    }
}
