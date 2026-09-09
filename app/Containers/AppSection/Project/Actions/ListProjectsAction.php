<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\ListProjectsTask;
use App\Containers\AppSection\Project\UI\WEB\Requests\ListProjectsRequest;
use App\Containers\AppSection\Task\Enums\TaskStatus;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListProjectsAction extends ParentAction
{
    public function __construct(
        private readonly ListProjectsTask $listProjectsTask
    ) {}

    /**
     * @param ListProjectsRequest $request
     * @return LengthAwarePaginator<int, Project>
     */
    public function run(ListProjectsRequest $request): LengthAwarePaginator
    {
        $user = $request->user();

        return $this->listProjectsTask->run(
            userId: $user->id,
            with: ['user', 'members'],
            withCount: [
                'tasks',
                'tasks as done_tasks_count' => function ($query) {
                    $query->where('tasks.status', TaskStatus::DONE);
                }
            ],
        );
    }
}