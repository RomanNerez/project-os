<?php

namespace App\Containers\AppSection\Project\Tasks;

use App\Containers\AppSection\Project\Data\Repositories\ProjectRepository;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Collection;

final class ListTaskCreatableProjectsTask extends ParentTask
{
    public function __construct(
        private readonly ProjectRepository $repository,
    ) {
    }

    /**
     * @param int $userId
     * @return Collection<int, Project>
     */
    public function run(int $userId): Collection
    {
        return $this->repository
            ->scopeTaskCreatableBy($userId)
            ->orderBy('title')
            ->all();
    }
}
