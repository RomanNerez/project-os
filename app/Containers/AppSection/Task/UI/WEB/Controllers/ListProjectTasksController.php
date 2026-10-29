<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\FindProjectByIdAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListProjectTasksController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
        private readonly FindProjectByIdAction $findProjectByIdAction,
    ) {}

    /**
     * @param ListTasksRequest $request
     * @param int $id
     * @return Response
     */
    public function __invoke(ListTasksRequest $request, int $id): Response
    {
        $project = $this->findProjectByIdAction->run($id);
        $paginator = $this->action->run($request, $project->id);

        $tasks = fractal($paginator, new TaskTransformer())
            ->parseIncludes(['project.user', 'project.members', 'assignee', 'media', 'comments.user', 'time_entries.user', 'activities.causer'])
            ->toArray();

        return Inertia::render('tasks', [
            'project' => fractal($project, new ProjectTransformer())->toArray(),
            'tasks' => Inertia::paginatedScroll($tasks, $paginator),
        ]);
    }
}
