<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\CreateProjectTaskRequest;
use App\Containers\AppSection\Task\Actions\CreateTaskAction;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class CreateProjectTaskController extends WebController
{
    public function __construct(
        private readonly CreateTaskAction $action
    ) {}

    /**
     * @param CreateProjectTaskRequest $request
     * @param Project $project
     * @return Redirector|RedirectResponse
     */
    public function __invoke(CreateProjectTaskRequest $request, Project $project): Redirector|RedirectResponse
    {
        $this->action->run($project->id, $request->user()->id, $request->validated());

        return back();
    }
}
