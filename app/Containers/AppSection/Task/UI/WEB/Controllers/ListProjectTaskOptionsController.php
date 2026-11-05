<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use Apiato\Http\Response;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Controllers\WebController;

final class ListProjectTaskOptionsController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
    ) {}

    /**
     * @param ListTasksRequest $request
     * @param Project $project
     * @return Response
     */
    public function __invoke(ListTasksRequest $request, Project $project): Response
    {
        $tasks = $this->action->run($request, $project->id);

        return Response::create($tasks, TaskTransformer::class);
    }
}
