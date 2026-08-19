<?php

namespace App\Containers\AppSection\Task\Tasks;

use App\Containers\AppSection\Task\Data\Repositories\TaskRepository;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListTasksTask extends ParentTask
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {
    }

    /**
     * @return LengthAwarePaginator<int, Task>
     */
    public function run(): LengthAwarePaginator
    {
        return $this->repository
            ->addRequestCriteria()
            ->with(['project', 'assignee'])
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
