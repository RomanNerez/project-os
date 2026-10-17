<?php

namespace App\Containers\AppSection\Task\Tasks;

use App\Containers\AppSection\Task\Data\Repositories\TaskRepository;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class FindVisibleTaskTask extends ParentTask
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {
    }

    /**
     * The task with the given id, or `null` when it does not exist or the user cannot see it.
     *
     * @param int $id
     * @param int $userId
     * @return Task|null
     */
    public function run(int $id, int $userId): ?Task
    {
        return $this->repository
            ->scopeVisibleForUser($userId)
            ->findWhere(['id' => $id])
            ->first();
    }
}
