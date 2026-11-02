<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Actions\UpdateProjectMemberAction;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\UpdateProjectMemberRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class UpdateProjectMemberController extends WebController
{
    public function __construct(
        private readonly UpdateProjectMemberAction $action
    ) {}

    /**
     * @param UpdateProjectMemberRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(
        UpdateProjectMemberRequest $request,
        Project $project,
        User $member
    ): Redirector|RedirectResponse
    {
        $this->action->run($request, $project, $member);

        return back();
    }
}
