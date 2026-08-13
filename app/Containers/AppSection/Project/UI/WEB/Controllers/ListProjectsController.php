<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\ListProjectsAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Project\UI\WEB\Requests\ListProjectsRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListProjectsController extends WebController
{
    public function __construct(
        private readonly ListProjectsAction $action
    ) {}

    /**
     * @param ListProjectsRequest $request
     * @return Response
     */
    public function __invoke(ListProjectsRequest $request): Response
    {
        $projects = $this->action->run();
        
        return Inertia::render('projects', [
            'projects' => fractal($projects, new ProjectTransformer())->toArray(),
        ]);
    }
}
