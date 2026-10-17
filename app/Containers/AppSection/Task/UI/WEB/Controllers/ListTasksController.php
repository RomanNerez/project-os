<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\ListTaskCreatableProjectsAction;
use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListTasksController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
        private readonly ListTaskCreatableProjectsAction $listTaskCreatableProjectsAction,
    ) {}

    /**
     * @param ListTasksRequest $request
     * @return Response
     */
    public function __invoke(ListTasksRequest $request): Response
    {
        $paginator = $this->action->run($request);
        $tasks = fractal($paginator, new TaskTransformer())
            ->parseIncludes(['project.user', 'project.members', 'assignee', 'media', 'comments.user'])
            ->toArray();

        return Inertia::render('tasks', [
            'tasks' => Inertia::paginatedScroll($tasks, $paginator),
            'canCreateTasks' => $this->listTaskCreatableProjectsAction->run($request->user()->id)->isNotEmpty(),
        ]);
    }
}
