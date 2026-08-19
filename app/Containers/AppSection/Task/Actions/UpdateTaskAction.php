<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\UpdateTaskTask;
use App\Containers\AppSection\Task\UI\WEB\Requests\UpdateTaskRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class UpdateTaskAction extends ParentAction
{
    public function __construct(
        private readonly UpdateTaskTask $updateTaskTask
    ) {}

    /**
     * @param UpdateTaskRequest $request
     * @param int $id
     * @return Task
     */
    public function run(UpdateTaskRequest $request, int $id): Task
    {
        return $this->updateTaskTask->run($id, $request->validated());
    }
}
