<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\ListTasksTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListTasksAction extends ParentAction
{
    public function __construct(
        private readonly ListTasksTask $listTasksTask,
    ) {
    }

    /**
     * @return LengthAwarePaginator<int, Task>
     */
    public function run(): LengthAwarePaginator
    {
        return $this->listTasksTask->run(with: [
            'project',
            'assignee',
            'comments' => fn($query) => $query->with('user')->orderBy('created_at', 'desc')
        ]);
    }
}
