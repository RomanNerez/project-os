<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Models\Finance;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class SyncFinanceTaskPaymentsTask extends ParentTask
{
    /**
     * Replaces the record's finance_task rows. A (task, user) pair that was already paid
     * keeps its saved hourly rate, so editing a record never picks up a newer project rate.
     *
     * @param Finance $finance
     * @param list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}> $rows
     * @return void
     */
    public function run(Finance $finance, array $rows): void
    {
        $savedRates = $finance->taskPayments()->get()->mapWithKeys(
            static fn ($task): array => ["{$task->id}:{$task->pivot->user_id}" => $task->pivot->hourly_rate],
        );

        $finance->tasks()->detach();

        foreach ($rows as $row) {
            $key = "{$row['task_id']}:{$row['user_id']}";

            $finance->tasks()->attach($row['task_id'], [
                'user_id' => $row['user_id'],
                'seconds' => $row['seconds'],
                'hourly_rate' => $savedRates->has($key) ? $savedRates->get($key) : $row['hourly_rate'],
            ]);
        }

        $finance->unsetRelation('tasks');
    }
}
