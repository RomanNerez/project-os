<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\CreateProjectTask;
use App\Containers\AppSection\Project\UI\WEB\Requests\CreateProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class CreateProjectAction extends ParentAction
{
    public function __construct(
        private readonly CreateProjectTask $createProjectTask
    ) {}

    /**
     * @param CreateProjectRequest $request
     * @return Project
     */
    public function run(CreateProjectRequest $request): Project
    {
        return $this->createProjectTask->run($request->validated());
    }
}
