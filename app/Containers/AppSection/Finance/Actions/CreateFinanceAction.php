<?php

namespace App\Containers\AppSection\Finance\Actions;

use App\Containers\AppSection\Finance\Enums\FinanceCurrency;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Finance\Tasks\CreateFinanceTask;
use App\Containers\AppSection\Finance\UI\WEB\Requests\CreateFinanceRequest;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CreateFinanceAction extends ParentAction
{
    public function __construct(
        private readonly CreateFinanceTask $createFinanceTask,
    ) {
    }

    /**
     * Every task must be visible to the user; the UAH amount is always computed here,
     * whatever preview the client showed.
     *
     * @throws ValidationException
     */
    public function run(CreateFinanceRequest $request, int $userId): Finance
    {
        $data = $request->validated();
        $taskIds = array_values(array_unique(array_map('intval', $data['task_ids'])));

        if (Task::query()->visibleTo($userId)->whereIn('id', $taskIds)->count() !== \count($taskIds)) {
            throw ValidationException::withMessages(['task_ids' => 'Деякі задачі недоступні.']);
        }

        $isUah = FinanceCurrency::UAH->value === $data['currency'];
        $amount = (float) $data['amount'];
        $rate = $isUah ? null : (float) $data['exchange_rate'];

        return DB::transaction(function () use ($request, $data, $userId, $taskIds, $amount, $rate): Finance {
            $finance = $this->createFinanceTask->run([
                'user_id' => $userId,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'currency' => $data['currency'],
                'amount' => $amount,
                'exchange_rate' => $rate,
                'amount_uah' => round($amount * ($rate ?? 1), 2),
                'status' => $data['status'],
                'due_date' => $data['due_date'] ?? null,
                'paid_at' => $data['paid_at'] ?? null,
            ], $taskIds);

            if ($request->hasFile('receipts')) {
                $finance
                    ->addMultipleMediaFromRequest(['receipts'])
                    ->each(static function ($fileAdder) {
                        $fileAdder->toMediaCollection(Finance::RECEIPTS_COLLECTION);
                    });
            }

            return $finance;
        });
    }
}
