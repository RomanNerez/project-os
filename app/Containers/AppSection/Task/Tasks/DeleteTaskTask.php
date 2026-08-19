<?php

namespace App\Containers\AppSection\Task\Tasks;

use App\Containers\AppSection\Task\Data\Repositories\TaskRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class DeleteTaskTask extends ParentTask
{
    public function __construct(
        private readonly TaskRepository $repository,
    ) {
    }

    /**
     * @param int $id
     * @return bool
     */
    public function run(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
