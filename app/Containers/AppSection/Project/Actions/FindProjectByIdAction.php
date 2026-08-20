<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Project\Tasks\FindProjectByIdTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class FindProjectByIdAction extends ParentAction
{
    public function __construct(
        private readonly FindProjectByIdTask $findProjectByIdTask
    ) {}

    /**
     * @param int $id
     * @return Project
     */
    public function run(int $id): Project
    {
        return $this->findProjectByIdTask->run($id);
    }
}
