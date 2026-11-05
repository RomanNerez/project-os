<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Data\Repositories\FinanceRepository;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class UpdateFinanceTask extends ParentTask
{
    public function __construct(
        private readonly FinanceRepository $repository,
        private readonly SyncFinanceTaskPaymentsTask $syncFinanceTaskPaymentsTask,
    ) {
    }

    /**
     * @param int $id
     * @param array<string, mixed> $data
     * @param list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}> $taskRows
     * @return Finance
     */
    public function run(int $id, array $data, array $taskRows): Finance
    {
        $finance = $this->repository->update($data, $id);
        $this->syncFinanceTaskPaymentsTask->run($finance, $taskRows);

        return $finance;
    }
}
