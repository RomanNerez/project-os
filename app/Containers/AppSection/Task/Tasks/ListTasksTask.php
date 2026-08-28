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
     * @param array<int, string> $with
     * @return LengthAwarePaginator<int, Task>
     */
    public function run(array $with = []): LengthAwarePaginator
    {
        return $this->repository
            ->addRequestCriteria()
            ->with($with)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
