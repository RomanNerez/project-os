<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\ListTaskCreatableProjectsAction;
use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ListTasksController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
        private readonly ListTaskCreatableProjectsAction $listTaskCreatableProjectsAction,
    ) {}

    /**
     * Inertia visits get the `tasks` page; plain JSON requests (Inertia `useHttp`,
     * e.g. TaskSelect) get just the transformed list with each task's project.
     *
     * @param ListTasksRequest $request
     * @return Response|JsonResponse
     */
    public function __invoke(ListTasksRequest $request): Response|JsonResponse
    {
        $paginator = $this->action->run($request);

        if ($request->wantsJson()) {
            return response()->json(
                fractal($paginator->getCollection(), new TaskTransformer())->parseIncludes(['project'])->toArray()
            );
        }
        $tasks = fractal($paginator, new TaskTransformer())
            ->parseIncludes(['project.user', 'project.members', 'assignee', 'media', 'comments.user'])
            ->toArray();

        return Inertia::render('tasks', [
            'tasks' => Inertia::paginatedScroll($tasks, $paginator),
            'canCreateTasks' => $this->listTaskCreatableProjectsAction->run($request->user()->id)->isNotEmpty(),
        ]);
    }
}
