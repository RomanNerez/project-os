<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\AddProjectMemberAction;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\AddProjectMemberRequest;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class AddProjectMemberController extends WebController
{
    public function __construct(
        private readonly AddProjectMemberAction $action
    ) {}

    /**
     * @param AddProjectMemberRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(AddProjectMemberRequest $request, Project $project): Redirector|RedirectResponse
    {
        $this->action->run($request, $project);
        
        return back();
    }
}
