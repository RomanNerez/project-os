<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\UpdateProjectTask;
use App\Containers\AppSection\Project\UI\WEB\Requests\UpdateProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateProjectAction extends ParentAction
{
    public function __construct(
        private readonly UpdateProjectTask $updateProjectTask
    ) {}

    /**
     * @param UpdateProjectRequest $request
     * @param int $id
     * @return Project
     */
    public function run(UpdateProjectRequest $request, int $id): Project
    {
        return $this->updateProjectTask->run($id, $request->validated());
    }
}
