<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Data\Repositories\FinanceRepository;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class CreateFinanceTask extends ParentTask
{
    public function __construct(
        private readonly FinanceRepository $repository,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @param array<int, int> $taskIds
     * @return Finance
     */
    public function run(array $data, array $taskIds): Finance
    {
        $finance = $this->repository->create($data);
        $finance->tasks()->sync($taskIds);

        return $finance;
    }
}
