<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\UpdateTaskTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateTaskAction extends ParentAction
{
    public function __construct(
        private readonly UpdateTaskTask $updateTaskTask
    ) {}

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Task
     */
    public function run(int $id, array $data): Task
    {
        return $this->updateTaskTask->run($id, $data);
    }
}
