<?php

namespace App\Containers\AppSection\Task\Actions;

use App\Containers\AppSection\Task\Models\Task;
use App\Containers\AppSection\Task\Tasks\CreateTaskTask;
use App\Ship\Parents\Actions\Action as ParentAction;

final class CreateTaskAction extends ParentAction
{
    public function __construct(
        private readonly CreateTaskTask $createTaskTask
    ) {}

    /**
     * @param int $projectId
     * @param int $userId
     * @param array<string, mixed> $data
     * @return Task
     */
    public function run(int $projectId, int $userId, array $data): Task
    {
        return $this->createTaskTask->run([
            'project_id' => $projectId,
            'user_id' => $userId,
            ...array_filter($data, static fn (mixed $value): bool => null !== $value),
        ]);
    }
}
