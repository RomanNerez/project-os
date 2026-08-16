<?php

namespace App\Containers\AppSection\Project\Tasks;

use App\Containers\AppSection\Project\Data\Repositories\ProjectRepository;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class DeleteProjectTask extends ParentTask
{
    public function __construct(
        private readonly ProjectRepository $repository,
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
