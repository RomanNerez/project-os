<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\CreateTaskTask;
use App\Containers\AppSection\Task\UI\WEB\Requests\CreateTaskRequest;
use App\Ship\Parents\Actions\Action as ParentAction;

final class CreateTaskAction extends ParentAction
{
    public function __construct(
        private readonly CreateTaskTask $createTaskTask
    ) {}

    /**
     * @param CreateTaskRequest $request
     * @return Task
     */
    public function run(CreateTaskRequest $request): Task
    {
        return $this->createTaskTask->run($request->validated());
    }
}
