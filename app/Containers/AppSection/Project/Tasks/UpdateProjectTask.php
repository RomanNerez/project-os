<?php

namespace App\Containers\AppSection\Project\Tasks;

use App\Containers\AppSection\Project\Data\Repositories\ProjectRepository;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateProjectTask extends ParentTask
{
    public function __construct(
        private readonly ProjectRepository $repository,
    ) {
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @return Project
     */
    public function run(int $id, array $data): Project
    {
        return $this->repository->update($data, $id);
    }
}
