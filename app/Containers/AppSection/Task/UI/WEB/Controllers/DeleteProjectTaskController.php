<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Actions\DeleteTaskAction;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\UI\WEB\Requests\DeleteProjectTaskRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class DeleteProjectTaskController extends WebController
{
    public function __construct(
        private readonly DeleteTaskAction $action
    ) {}

    /**
     * @param DeleteProjectTaskRequest $request
     * @param Project $project
     * @param Task $task
     * @return RedirectResponse
     */
    public function __invoke(DeleteProjectTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->action->run($task->id);

        return back();
    }
}
