<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\UpdateProjectAction;
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
     * @return Redirector|RedirectResponse
     */
    public function __invoke(UpdateProjectRequest $request, int $id): Redirector|RedirectResponse
    {
        $this->action->run($request, $id);
        
        return to_route('projects.index');
    }
}
