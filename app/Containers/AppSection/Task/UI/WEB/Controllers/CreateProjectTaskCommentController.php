<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Actions\CreateTaskCommentAction;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\UI\WEB\Requests\CreateProjectTaskCommentRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class CreateProjectTaskCommentController extends WebController
{
    public function __construct(
        private readonly CreateTaskCommentAction $action
    ) {}

    /**
     * @param CreateProjectTaskCommentRequest $request
     * @param Project $project
     * @param Task $task
     * @return RedirectResponse
     */
    public function __invoke(CreateProjectTaskCommentRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->action->run($task, $request->user()->id, $request->string('body')->toString());

        return back();
    }
}
