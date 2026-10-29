<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\FindProjectByIdAction;
use App\Containers\AppSection\Project\UI\API\Transformers\ProjectTransformer;
use App\Containers\AppSection\Project\UI\WEB\Requests\ListProjectMembersRequest;
use App\Ship\Parents\Controllers\WebController;
use Inertia\Inertia;
use Inertia\Response;

final class ListProjectMembersController extends WebController
{
    public function __construct(
        private readonly FindProjectByIdAction $action,
    ) {}

    public function __invoke(ListProjectMembersRequest $request, int $id): Response
    {
        $project = $this->action->run($id);
        $project->loadMissing('members.media');

        return Inertia::render('project-members', [
            'project' => fractal($project, new ProjectTransformer())
                ->parseIncludes(['members'])
                ->toArray(),
        ]);
    }
}
