<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\UpdateProjectTaskRequest;
use App\Containers\AppSection\Task\Actions\UpdateTaskAction;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UpdateProjectTaskController extends WebController
{
    public function __construct(
        private readonly UpdateTaskAction $action
    ) {}

    /**
     * @param UpdateProjectTaskRequest $request
     * @param Project $project
     * @param Task $task
     * @return RedirectResponse
     */
    public function __invoke(UpdateProjectTaskRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->action->run($task->id, $request->validated());

        return back();
    }
}
