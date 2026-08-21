<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\ListProjectTasksTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Collection;

final class ListProjectTasksAction extends ParentAction
{
    public function __construct(
        private readonly ListProjectTasksTask $listProjectTasksTask
    ) {}

    /**
     * @param int $projectId
     * @return Collection<int, Task>
     */
    public function run(int $projectId): Collection
    {
        return $this->listProjectTasksTask->run($projectId);
    }
}
