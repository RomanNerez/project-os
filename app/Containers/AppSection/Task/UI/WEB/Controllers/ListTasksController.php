<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;

final class ListTasksController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
    ) {}

    /**
     * Visible tasks with their project as plain JSON for Inertia `useHttp` (e.g. TaskSelect).
     * The tasks page itself lives under a project, see ListProjectTasksController.
     *
     * @param ListTasksRequest $request
     * @return JsonResponse
     */
    public function __invoke(ListTasksRequest $request): JsonResponse
    {
        $paginator = $this->action->run($request);

        return response()->json(
            fractal($paginator->getCollection(), new TaskTransformer())->parseIncludes(['project'])->toArray()
        );
    }
}
