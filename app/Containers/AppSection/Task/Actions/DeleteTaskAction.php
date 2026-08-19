<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Tasks\DeleteTaskTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class DeleteTaskAction extends ParentAction
{
    public function __construct(
        private readonly DeleteTaskTask $deleteTaskTask
    ) {}

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->deleteTaskTask->run($id);
    }
}
