<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\UpdateProjectAction;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\UpdateProjectRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class UpdateProjectController extends WebController
{
    public function __construct(
        private readonly UpdateProjectAction $action
    ) {}

    /**
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return Redirector|RedirectResponse
     */
    public function __invoke(UpdateProjectRequest $request, Project $project): Redirector|RedirectResponse
    {
        $this->action->run($request, $project->id);
        
        return back();
    }
}
