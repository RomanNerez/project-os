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
     * @param null|int $userId
     * @return self
     */
    public function filterByUserId(?int $userId): self
    {
        $this->scopeQuery(fn($query) =>
            $query->when(
                $userId,
                fn () => $query->where('user_id', $userId)
            )
        );

        return $this;
    }
}
