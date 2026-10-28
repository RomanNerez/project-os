<?php

namespace App\Containers\AppSection\Finance\Actions;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tasks\AddFinanceReceiptsTask;
use App\Containers\AppSection\Finance\Tasks\PrepareFinanceDataTask;
use App\Containers\AppSection\Finance\Tasks\UpdateFinanceTask;
use App\Containers\AppSection\Finance\UI\WEB\Requests\UpdateFinanceRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateFinanceAction extends ParentAction
{
    public function __construct(
        private readonly PrepareFinanceDataTask $prepareFinanceDataTask,
        private readonly UpdateFinanceTask $updateFinanceTask,
        private readonly AddFinanceReceiptsTask $addFinanceReceiptsTask,
    ) {
    }

    /**
     * Removed receipts are looked up among this record's receipts only, so foreign media ids are ignored.
     *
     * @throws ValidationException
     */
    public function run(UpdateFinanceRequest $request, Finance $finance): Finance
    {
        $data = $request->validated();
        [$attributes, $taskIds] = $this->prepareFinanceDataTask->run($data, $finance->project_id);
        $removedIds = array_map('intval', $data['removed_receipt_ids'] ?? []);

        return DB::transaction(function () use ($request, $finance, $attributes, $taskIds, $removedIds): Finance {
            $finance->getMedia(Finance::RECEIPTS_COLLECTION)
                ->whereIn('id', $removedIds)
                ->each->delete();

            $finance = $this->updateFinanceTask->run($finance->id, $attributes, $taskIds);

            $this->addFinanceReceiptsTask->run($finance, $request);

            return $finance;
        });
    }
}
