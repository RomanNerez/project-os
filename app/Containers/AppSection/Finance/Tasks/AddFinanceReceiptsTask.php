<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Parents\Requests\Request;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class AddFinanceReceiptsTask extends ParentTask
{
    /**
     * @param Finance $finance
     * @param Request $request
     * @return void
     */
    public function run(Finance $finance, Request $request): void
    {
        if (!$request->hasFile('receipts')) {
            return;
        }

        $finance
            ->addMultipleMediaFromRequest(['receipts'])
            ->each(static function ($fileAdder) {
                $fileAdder->toMediaCollection(Finance::RECEIPTS_COLLECTION);
            });
    }
}
