<?php

namespace App\Containers\AppSection\Project\Policies;

use App\Containers\AppSection\Authorization\Enums\Permission;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\User\Models\User;

final class ProjectPolicy extends BasePolicy
{
    /**
     * Workspace-level: granted by a workspace role with the "projects.create" permission.
     */
    public function create(User $user): bool
    {
        return $user->isSuperAdmin()
            || $user->checkPermissionTo(Permission::PROJECTS_CREATE->value, Permission::GUARD);
    }

    public function view(User $user, Project $project): bool
    {
        return $project->hasParticipant($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->isOwnerOrAdmin($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }

    public function createTask(User $user, Project $project): bool
    {
        return $this->canManageTasks($user, $project);
    }

    public function updateTask(User $user, Project $project): bool
    {
        return $this->canManageTasks($user, $project);
    }

    public function deleteTask(User $user, Project $project): bool
    {
        return $this->isOwnerOrAdmin($user, $project);
    }
}
