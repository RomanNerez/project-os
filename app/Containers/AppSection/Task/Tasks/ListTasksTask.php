<?php

namespace App\Containers\AppSection\Task\Tasks;

use App\Containers\AppSection\Task\Data\Repositories\TaskRepository;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Criteria\ThisEqualThatCriteria;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListTasksTask extends ParentTask
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {
    }

    /**
     * @param int $userId
     * @param array<int, string> $with
     * @param int|null $projectId limits the list to one project's tasks
     * @return LengthAwarePaginator<int, Task>
     */
    public function run(int $userId, array $with = [], ?int $projectId = null): LengthAwarePaginator
    {
        if ($projectId !== null) {
            $this->repository->pushCriteria(new ThisEqualThatCriteria('project_id', (string) $projectId));
        }

        return $this->repository
            ->addRequestCriteria()
            ->with($with)
            ->scopeVisibleForUser($userId)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
