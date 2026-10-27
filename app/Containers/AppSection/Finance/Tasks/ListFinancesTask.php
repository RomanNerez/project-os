<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Data\Repositories\FinanceRepository;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Criteria\ThisEqualThatCriteria;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListFinancesTask extends ParentTask
{
    public function __construct(
        private readonly FinanceRepository $repository,
    ) {
    }

    /**
     * @param int $userId
     * @param int $projectId
     * @return LengthAwarePaginator<int, Finance>
     */
    public function run(int $userId, int $projectId): LengthAwarePaginator
    {
        return $this->repository
            ->pushCriteria(new ThisEqualThatCriteria('project_id', (string) $projectId))
            ->addRequestCriteria()
            ->with(['project', 'tasks', 'media'])
            ->scopeVisibleForUser($userId)
            ->orderBy('created_at', 'desc')
            ->paginate();
    }
}
