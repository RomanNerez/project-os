<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Enums\FinanceCurrency;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Validation\ValidationException;

final class PrepareFinanceDataTask extends ParentTask
{
    /**
     * Every task must belong to the record's project;
     * the UAH amount is always computed here, whatever preview the client showed.
     *
     * @param array<string, mixed> $data validated create/update payload
     * @param int $projectId
     * @return array{0: array<string, mixed>, 1: array<int, int>} record attributes and task ids
     *
     * @throws ValidationException
     */
    public function run(array $data, int $projectId): array
    {
        $taskIds = array_values(array_unique(array_map('intval', $data['task_ids'] ?? [])));

        if ([] !== $taskIds && Task::query()->where('project_id', $projectId)->whereIn('id', $taskIds)->count() !== \count($taskIds)) {
            throw ValidationException::withMessages(['task_ids' => 'Задачі мають належати проєкту.']);
        }

        $isUah = FinanceCurrency::UAH->value === $data['currency'];
        $amount = (float) $data['amount'];
        $rate = $isUah ? null : (float) $data['exchange_rate'];

        return [[
            'project_id' => $projectId,
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
        ], $taskIds];
    }
}
