<?php

namespace App\Containers\AppSection\Project\Tasks;

use App\Containers\AppSection\Project\Data\Repositories\ProjectRepository;
use App\Containers\AppSection\Project\Models\Project;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListProjectsTask extends ParentTask
{
    public function __construct(
        private readonly ProjectRepository $repository,
    ) {
    }

    /**
     * @param int $userId
     * @param array<int, mixed> $with
     * @param array<int|string, mixed> $withCount
     * @param array<int, string> $excludeRoles
     * @return LengthAwarePaginator<int, Project>
     */
    public function run(int $userId, array $with = [], array $withCount = [], array $excludeRoles = []): LengthAwarePaginator
    {
        return $this->repository
            ->addRequestCriteria()
            ->with($with)
            ->withCount($withCount)
            ->scopeAuthUserId($userId, $excludeRoles)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
