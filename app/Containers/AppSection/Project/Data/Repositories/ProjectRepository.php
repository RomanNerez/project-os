<?php

namespace App\Containers\AppSection\Project\Data\Repositories;

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
        // 'id' => '=',
    ];

    /**
     * @param int $userId
     * @return self
     */
    public function scopeAuthUserId(int $userId): self
    {
        $this->scopeQuery(fn($query) =>
            $query
                ->where('user_id', $userId)
                ->orWhereHas('members', fn($query) => $query->where('users.id', $userId))

        );

        return $this;
    }
}
