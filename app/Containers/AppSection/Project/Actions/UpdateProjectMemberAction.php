<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\UpdateProjectMemberRequest;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateProjectMemberAction extends ParentAction
{
    /**
     * @param UpdateProjectMemberRequest $request
     * @return void
     */
    public function run(UpdateProjectMemberRequest $request, Project $project, User $member): void
    {
        $project->members()->updateExistingPivot($member->id, [
            'role' => $request->role,
            'hourly_rate' => $request->hourly_rate,
        ]);
    }
}
