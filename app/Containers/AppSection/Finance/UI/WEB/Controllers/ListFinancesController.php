<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Controllers;

use App\Containers\AppSection\Finance\Actions\ListFinancesAction;
use App\Containers\AppSection\Project\Actions\FindProjectByIdAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Finance\UI\API\Transformers\FinanceTransformer;
use App\Containers\AppSection\Finance\UI\WEB\Requests\ListFinancesRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListFinancesController extends WebController
{
    public function __construct(
        private readonly ListFinancesAction $action,
        private readonly FindProjectByIdAction $findProjectByIdAction,
    ) {
    }

    public function __invoke(ListFinancesRequest $request, int $id): Response
    {
        $project = $this->findProjectByIdAction->run($id);
        $paginator = $this->action->run($request->user('web')->id, $project->id);

        $finances = fractal($paginator, new FinanceTransformer())
            ->parseIncludes(['tasks', 'media'])
            ->toArray();

        return Inertia::render('finances', [
            'project' => fractal($project, new ProjectTransformer())->toArray(),
            'finances' => Inertia::paginatedScroll($finances, $paginator),
        ]);
    }
}
