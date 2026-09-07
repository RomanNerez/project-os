<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\UI\WEB\Requests\AddProjectMemberRequest;
use App\Containers\AppSection\User\Tasks\FindUserByEmailTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class AddProjectMemberAction extends ParentAction
{
    public function __construct(
        private readonly FindUserByEmailTask $findUserByEmailTask
    ) {}

    /**
     * @param AddProjectMemberRequest $request
     * @return void
     */
    public function run(AddProjectMemberRequest $request, Project $project): void
    {
        $user = $this->findUserByEmailTask->run($request->email);

        if (empty($user)) return;

        $project->members()->syncWithoutDetaching([
            $user->id => ['role' => $request->role]
        ]);
    }
}
