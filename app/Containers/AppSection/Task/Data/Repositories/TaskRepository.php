<?php

namespace App\Containers\AppSection\Task\Data\Repositories;

use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Repositories\Repository as ParentRepository;

/**
 * @template TModel of Task
 *
 * @extends ParentRepository<TModel>
 */
final class TaskRepository extends ParentRepository
{
    protected $fieldSearchable = [
        // 'id' => '=',
    ];

    /**
     * @param int $userId
     * @return self
     */
    public function filterByUserId(int $userId): self
    {
        $this->scopeQuery(fn($query) =>
            $query->when(
                $userId,
                fn () => $query
                    ->where('user_id', $userId)
                    ->orWhere('assignee_id', $userId)
            )
        );

        return $this;
    }
}
