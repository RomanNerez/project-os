<?php

namespace App\Containers\AppSection\Task\UI\WEB\Controllers;

use App\Containers\AppSection\Task\Actions\ListTasksAction;
use App\Containers\AppSection\Task\UI\API\Transformers\TaskTransformer;
use App\Containers\AppSection\Task\UI\WEB\Requests\ListTasksRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;

final class ListProjectTaskOptionsController extends WebController
{
    public function __construct(
        private readonly ListTasksAction $action,
    ) {}

    /**
     * Tasks of one project with their project as plain JSON for Inertia `useHttp` (e.g. TaskSelect).
     * The project tasks page itself is ListProjectTasksController.
     *
     * @param ListTasksRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function __invoke(ListTasksRequest $request, int $id): JsonResponse
    {
        $paginator = $this->action->run($request, $id);

        return response()->json(
            fractal($paginator->getCollection(), new TaskTransformer())->parseIncludes(['project'])->toArray()
        );
    }
}
