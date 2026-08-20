<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\FindProjectByIdAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Project\UI\WEB\Requests\ShowProjectRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ShowProjectController extends WebController
{
    public function __construct(
        private readonly FindProjectByIdAction $action
    ) {}

    /**
     * @param ShowProjectRequest $request
     * @return Response
     */
    public function __invoke(ShowProjectRequest $request, int $id): Response
    {
        $project = $this->action->run($id);

        return Inertia::render('project', [
            'project' => fractal($project, new ProjectTransformer())->toArray(),
        ]);
    }
}
