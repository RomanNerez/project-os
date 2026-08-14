<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\UpdateOrCreateProjectTask;
use App\Containers\AppSection\Project\UI\WEB\Requests\StoreProjectRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class StoreProjectAction extends ParentAction
{
    public function __construct(
        private readonly UpdateOrCreateProjectTask $updateOrCreateProjectTask
    ) {}

    /**
     * @param StoreProjectRequest $request
     * @return Project
     */
    public function run(StoreProjectRequest $request): Project
    {
        $attributes = $request->only(['title', 'description', 'status', 'budget']);
        $value = $request->has('id') ? [$request->input('id')] : [];

        return $this->updateOrCreateProjectTask->run($attributes, $value);
    }
}