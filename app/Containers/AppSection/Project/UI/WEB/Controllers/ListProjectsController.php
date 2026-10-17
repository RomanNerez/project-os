<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\ListProjectsAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Project\UI\WEB\Requests\ListProjectsRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

final class ListProjectsController extends WebController
{
    public function __construct(
        private readonly ListProjectsAction $action
    ) {}

    /**
     * Inertia visits get the `projects` page; plain JSON requests (Inertia `useHttp`,
     * e.g. ProjectSelect) get just the transformed list without includes.
     *
     * @param ListProjectsRequest $request
     * @return Response|JsonResponse
     */
    public function __invoke(ListProjectsRequest $request): Response|JsonResponse
    {
        $paginator = $this->action->run($request);

        if ($request->wantsJson()) {
            return response()->json(
                fractal($paginator->getCollection(), new ProjectTransformer())->toArray()
            );
        }

        $projects = fractal($paginator->getCollection(), new ProjectTransformer())
            ->parseIncludes(['user', 'members', 'task_status_counts'])
            ->toArray();

        return Inertia::render('projects', [
            'projects' => Inertia::paginatedScroll($projects, $paginator),
        ]);
    }
}
