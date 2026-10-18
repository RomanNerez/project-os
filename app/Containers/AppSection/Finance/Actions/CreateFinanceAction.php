<?php

namespace App\Containers\AppSection\Finance\Actions;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tasks\AddFinanceReceiptsTask;
use App\Containers\AppSection\Finance\Tasks\CreateFinanceTask;
use App\Containers\AppSection\Finance\Tasks\PrepareFinanceDataTask;
use App\Containers\AppSection\Finance\UI\WEB\Requests\CreateFinanceRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateFinanceAction extends ParentAction
{
    public function __construct(
        private readonly PrepareFinanceDataTask $prepareFinanceDataTask,
        private readonly CreateFinanceTask $createFinanceTask,
        private readonly AddFinanceReceiptsTask $addFinanceReceiptsTask,
    ) {
    }

    /**
     * @throws ValidationException
     */
    public function run(CreateFinanceRequest $request, int $userId): Finance
    {
        [$attributes, $taskIds] = $this->prepareFinanceDataTask->run($request->validated(), $userId);

        return DB::transaction(function () use ($request, $attributes, $userId, $taskIds): Finance {
            $finance = $this->createFinanceTask->run(['user_id' => $userId, ...$attributes], $taskIds);

            $this->addFinanceReceiptsTask->run($finance, $request);

            return $finance;
        });
    }
}
