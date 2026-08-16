<?php

namespace App\Containers\AppSection\Project\Actions;

use App\Containers\AppSection\Project\Tasks\DeleteProjectTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class DeleteProjectAction extends ParentAction
{
    public function __construct(
        private readonly DeleteProjectTask $deleteProjectTask
    ) {}

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->deleteProjectTask->run($id);
    }
}
