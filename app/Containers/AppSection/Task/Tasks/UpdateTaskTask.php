<?php

namespace App\Containers\AppSection\Task\Tasks;

use App\Containers\AppSection\Task\Data\Repositories\TaskRepository;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateTaskTask extends ParentTask
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Task
     */
    public function run(int $id, array $data): Task
    {
        return $this->repository->update($data, $id);
    }
}
