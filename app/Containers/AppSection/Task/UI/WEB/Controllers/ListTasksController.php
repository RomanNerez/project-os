<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\ListAllProjectsAction;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Containers\AppSection\User\Actions\ListAllUsersAction;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListTasksController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
        private readonly ListAllProjectsAction $listAllProjectsAction,
        private readonly ListAllUsersAction $listAllUsersAction,
    ) {}

    /**
     * @param ListTasksRequest $request
     * @return Response
     */
    public function __invoke(ListTasksRequest $request): Response
    {
        $tasks = $this->action->run();
        $tasks = fractal($tasks, new TaskTransformer())
            ->parseIncludes(['project', 'assignee', 'media'])
            ->toArray();

        return Inertia::render('tasks', [
            'tasks' => $tasks,
            'projects' => $this->listAllProjectsAction->run()
                ->map(static fn (Project $project): array => [
                    'id' => $project->id,
                    'title' => $project->title,
                ])->values(),
            'assignees' => $this->listAllUsersAction->run()
                ->map(static fn (User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                ])->values(),
        ]);
    }
}
