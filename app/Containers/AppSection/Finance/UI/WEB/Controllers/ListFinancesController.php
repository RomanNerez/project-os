<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Controllers;

use App\Containers\AppSection\Finance\Actions\ListFinancesAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Finance\UI\API\Transformers\FinanceTransformer;
use App\Containers\AppSection\Finance\UI\WEB\Requests\ListFinancesRequest;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListFinancesController extends WebController
{
    public function __construct(
        private readonly ListFinancesAction $action,
    ) {
    }

    /**
     * @param ListFinancesRequest $request
     * @param Project $project
     * @return Response
     */
    public function __invoke(ListFinancesRequest $request, Project $project): Response
    {
        $paginator = $this->action->run($request->user('web')->id, $project->id);

        $finances = fractal($paginator, new FinanceTransformer())
            ->parseIncludes(['project', 'tasks', 'media'])
            ->toArray();

        return Inertia::render('finances', [
            'project' => fractal($project, new ProjectTransformer())->toArray(),
            'finances' => Inertia::paginatedScroll($finances, $paginator),
        ]);
    }
}
