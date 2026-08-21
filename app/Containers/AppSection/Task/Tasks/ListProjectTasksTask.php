<?php

namespace App\Containers\AppSection\Task\Tasks;

use App\Containers\AppSection\Task\Data\Repositories\TaskRepository;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Collection;

final class ListProjectTasksTask extends ParentTask
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {
    }

    /**
     * @param int $projectId
     * @return Collection<int, Task>
     */
    public function run(int $projectId): Collection
    {
        return $this->repository
            ->with(['project', 'assignee'])
            ->orderBy('created_at')
            ->findWhere(['project_id' => $projectId]);
    }
}
