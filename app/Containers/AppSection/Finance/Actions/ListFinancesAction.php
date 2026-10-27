<?php

namespace App\Containers\AppSection\Finance\Actions;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tasks\ListFinancesTask;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListFinancesAction extends ParentAction
{
    public function __construct(
        private readonly ListFinancesTask $listFinancesTask,
    ) {
    }

    /**
     * @param int $userId
     * @param int $projectId
     * @return LengthAwarePaginator<int, Finance>
     */
    public function run(int $userId, int $projectId): LengthAwarePaginator
    {
        return $this->listFinancesTask->run($userId, $projectId);
    }
}
