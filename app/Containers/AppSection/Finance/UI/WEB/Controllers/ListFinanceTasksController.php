<?php

namespace App\Containers\AppSection\Finance\UI\WEB\Controllers;

use Apiato\Http\Response;
use App\Containers\AppSection\Finance\Actions\ListFinanceTasksAction;
use App\Containers\AppSection\Finance\UI\API\Transformers\FinanceTaskHoursTransformer;
use App\Containers\AppSection\Finance\UI\WEB\Requests\ListFinanceTasksRequest;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Validation\ValidationException;

final class ListFinanceTasksController extends WebController
{
    public function __construct(
        private readonly ListFinanceTasksAction $action,
    ) {
    }

    /**
     * JSON for the finance form: tasks with unpaid tracked time.
     *
     * @param ListFinanceTasksRequest $request
     * @param Project $project
     * @return Response
     *
     * @throws ValidationException
     */
    public function __invoke(ListFinanceTasksRequest $request, Project $project): Response
    {
        return Response::create($this->action->run($request, $project->id), FinanceTaskHoursTransformer::class);
    }
}
