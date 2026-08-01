<?php

namespace App\Containers\AppSection\Project\Tasks;

use App\Containers\AppSection\Project\Data\Repositories\ProjectRepository;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateOrCreateProjectTask extends ParentTask
{
    public function __construct(
        private readonly ProjectRepository $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $attributes
     * @param array<string, mixed> $values
     * @return Project
     */
    public function run(array $attributes, array $values = []): Project
    {
        return $this->repository->updateOrCreate($attributes, $values);
    }
}
