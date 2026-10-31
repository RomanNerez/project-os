<?php

namespace App\Containers\AppSection\Project\Data\Repositories;

use App\Containers\AppSection\Project\Enums\ProjectRole;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Project
 *
 * @extends ParentRepository<TModel>
 */
final class ProjectRepository extends ParentRepository
{
    protected $fieldSearchable = [
        'title' => 'ilike',
        'status' => 'in',
    ];

    /**
     * Projects the user owns or is a member of. `$excludeRoles` hides projects where
     * the user is a member with one of these roles (owners are never excluded).
     *
     * @param int $userId
     * @param array<int, string> $excludeRoles
     * @return self
     */
    public function scopeAuthUserId(int $userId, array $excludeRoles = []): self
    {
        $this->scopeQuery(fn($query) => $query->where(
            fn($query) => $query
                ->where('user_id', $userId)
                ->orWhereHas('members', fn($query) => $query
                    ->where('users.id', $userId)
                    ->when([] !== $excludeRoles, fn($query) => $query->whereNotIn('project_user.role', $excludeRoles)))
        ));

        return $this;
    }

    /**
     * Owner, admin or member (mirrors BasePolicy::canManageTasks).
     *
     * @param int $userId
     * @return self
     */
    public function scopeTaskCreatableBy(int $userId): self
    {
        $this->scopeQuery(fn($query) => $query->where(
            fn($query) => $query
                ->where('user_id', $userId)
                ->orWhereHas('members', fn($query) => $query
                    ->where('users.id', $userId)
                    ->whereIn('project_user.role', [ProjectRole::ADMIN->value, ProjectRole::EXECUTOR->value]))
        ));

        return $this;
    }
}
