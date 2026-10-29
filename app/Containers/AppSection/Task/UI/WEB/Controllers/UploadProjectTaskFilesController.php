<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Actions\UploadTaskFilesAction;
use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\UI\WEB\Requests\UploadProjectTaskFilesRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;

final class UploadProjectTaskFilesController extends WebController
{
    public function __construct(
        private readonly UploadTaskFilesAction $action
    ) {}

    /**
     * @param UploadProjectTaskFilesRequest $request
     * @param Project $project
     * @param Task $task
     * @return RedirectResponse
     */
    public function __invoke(UploadProjectTaskFilesRequest $request, Project $project, Task $task): RedirectResponse
    {
        $this->action->run($task);

        return back();
    }
}
