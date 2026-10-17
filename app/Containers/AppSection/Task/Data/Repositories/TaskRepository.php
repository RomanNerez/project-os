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
        'title' => 'ilike',
        'status' => 'in',
        'project_id' => 'in',
    ];

    /**
     * @param int $userId
     * @return self
     */
    public function scopeVisibleForUser(int $userId): self
    {
        $this->scopeQuery(fn ($query) => $query->visibleTo($userId));

        return $this;
    }
}
