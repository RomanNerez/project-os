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
     * @return LengthAwarePaginator<int, Project>
     */
    public function run(): LengthAwarePaginator
    {
        return $this->repository
            ->addRequestCriteria()
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
