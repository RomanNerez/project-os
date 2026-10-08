<?php

namespace App\Containers\AppSection\Project\Policies;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;

final class ProjectMemberPolicy extends BasePolicy
{
    /**
     * @param User $user
     * @param Project $project
     * @return bool
     */
    public function addMember(User $user, Project $project): bool
    {
        return $this->isOwnerOrAdmin($user, $project);
    }

    /**
     * @param User $user
     * @param Project $project
     * @param User $member
     * @return bool
     */
    public function deleteMember(User $user, Project $project, User $member): bool
    {
        if ($project->isOwnedBy($member)) {
            return false;
        }

        return $this->isOwnerOrAdmin($user, $project);
    }
}
