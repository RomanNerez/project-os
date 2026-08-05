<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\UpdateOrCreateProjectTask;
use App\Containers\AppSection\Project\UI\WEB\Requests\CreateProjectRequest;
use App\Containers\AppSection\Project\UI\WEB\Requests\UpdateProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class StoreProjectAction extends ParentAction
{
    public function __construct(
        private readonly UpdateOrCreateProjectTask $updateOrCreateProjectTask
    ) {}

    /**
     * @param CreateProjectRequest|UpdateProjectRequest $request
     * @param null|int $id
     * @return Project
     */
    public function run(CreateProjectRequest|UpdateProjectRequest $request, ?int $id = null): Project
    {
        $values = $request->only(['title', 'description', 'status', 'budget']);
        $attributes = empty($id) ? [] : ['id' => $id];

        return $this->updateOrCreateProjectTask->run($attributes, $values);
    }
}