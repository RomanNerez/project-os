<?php

namespace App\Containers\AppSection\Project\UI\WEB\Controllers;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\DeleteProjectMemberRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\WebController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;

final class DeleteProjectMemberController extends WebController
{
    /**
     * @param DeleteProjectMemberRequest $request
     * @return Redirector|RedirectResponse
     */
    public function __invoke(
        DeleteProjectMemberRequest $request,
        Project $project,
        User $member
    ): Redirector|RedirectResponse
    {
        $project->members()->detach($member->id);
        
        return back();
    }
}
