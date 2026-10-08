<?php

namespace App\Containers\AppSection\Project\Policies;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;

final class ProjectPolicy extends BasePolicy
{
    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function update(User $user, Project $project): bool
    {
        return $this->isOwnerOrAdmin($user, $project);
    }

    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }
}
