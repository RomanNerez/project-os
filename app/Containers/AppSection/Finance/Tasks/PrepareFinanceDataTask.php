<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Enums\FinanceCurrency;
use App\Containers\AppSection\Finance\Models\Finance;
use App\Containers\AppSection\Project\Models\Project;
use App\Containers\AppSection\Task\Models\Task;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Validation\ValidationException;

final class PrepareFinanceDataTask extends ParentTask
{
    /**
     * Every task must belong to the record's project and be paid to at least one of its participants;
     * hourly rates come from project_user (USD), never from the client;
     * the UAH amount is always computed here, whatever preview the client showed.
     * The amount may not be lower than the paid hours cost: Σ seconds × hourly rate (USD),
     * converted with the exchange rate for UAH records. Pairs already paid by $finance keep their saved rate.
     *
     * @param array<string, mixed> $data validated create/update payload
     * @param int $projectId
     * @param null|Finance $finance the record being updated
     * @return array{0: array<string, mixed>, 1: list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}>} record attributes and finance_task rows
     *
     * @throws ValidationException
     */
    public function run(array $data, int $projectId, ?Finance $finance = null): array
    {
        $taskIds = array_values(array_unique(array_map('intval', $data['task_ids'] ?? [])));

        if ([] !== $taskIds && Task::query()->where('project_id', $projectId)->whereIn('id', $taskIds)->count() !== \count($taskIds)) {
            throw ValidationException::withMessages(['task_ids' => 'Задачі мають належати проєкту.']);
        }

        $rows = $this->paymentRows($data['task_payments'] ?? [], $taskIds, $projectId);

        $isUah = FinanceCurrency::UAH->value === $data['currency'];
        $amount = (float) $data['amount'];
        $rate = $isUah ? null : (float) $data['exchange_rate'];

        $this->assertCoversPayments($amount, $isUah, $data['exchange_rate'] ?? null, $this->withSavedRates($rows, $finance));

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
        ], $rows];
    }

    /**
     * One row per (task, user); a repeated pair keeps the last value sent.
     *
     * @param array<int, array<string, mixed>> $payments
     * @param array<int, int> $taskIds
     * @param int $projectId
     * @return list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}>
     *
     * @throws ValidationException
     */
    private function paymentRows(array $payments, array $taskIds, int $projectId): array
    {
        if ([] === $taskIds) {
            return [];
        }

        $project = Project::query()->with('members')->findOrFail($projectId);
        $rates = $project->members->mapWithKeys(
            static fn ($member): array => [(int) $member->id => $member->pivot->hourly_rate],
        );
        $participants = $rates->keys()->push((int) $project->user_id);

        $rows = [];

        foreach ($payments as $payment) {
            $taskId = (int) $payment['task_id'];
            $userId = (int) $payment['user_id'];

            if (!\in_array($taskId, $taskIds, true)) {
                throw ValidationException::withMessages(['task_payments' => 'Оплата має стосуватися вибраної задачі.']);
            }

            if (!$participants->contains($userId)) {
                throw ValidationException::withMessages(['task_payments' => 'Користувач має бути учасником проєкту.']);
            }

            $rows["{$taskId}:{$userId}"] = [
                'task_id' => $taskId,
                'user_id' => $userId,
                'seconds' => (int) ($payment['seconds'] ?? 0),
                'hourly_rate' => $rates->get($userId),
            ];
        }

        $paidTaskIds = array_column($rows, 'task_id');

        if ([] !== array_diff($taskIds, $paidTaskIds)) {
            throw ValidationException::withMessages(['task_payments' => 'Вкажіть користувача для кожної задачі.']);
        }

        return array_values($rows);
    }

    /**
     * @param list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}> $rows
     * @param null|Finance $finance
     * @return list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}>
     */
    private function withSavedRates(array $rows, ?Finance $finance): array
    {
        if (null === $finance || [] === $rows) {
            return $rows;
        }

        $savedRates = $finance->taskPayments()->get()->mapWithKeys(
            static fn ($task): array => ["{$task->id}:{$task->pivot->user_id}" => $task->pivot->hourly_rate],
        );

        return array_map(static function (array $row) use ($savedRates): array {
            $key = "{$row['task_id']}:{$row['user_id']}";

            return $savedRates->has($key) ? [...$row, 'hourly_rate' => $savedRates->get($key)] : $row;
        }, $rows);
    }

    /**
     * @param float $amount
     * @param bool $isUah
     * @param mixed $exchangeRate
     * @param list<array{task_id: int, user_id: int, seconds: int, hourly_rate: string|null}> $rows
     * @return void
     *
     * @throws ValidationException
     */
    private function assertCoversPayments(float $amount, bool $isUah, mixed $exchangeRate, array $rows): void
    {
        $minUsd = array_sum(array_map(
            static fn (array $row): float => $row['seconds'] / 3600 * (float) ($row['hourly_rate'] ?? 0),
            $rows,
        ));

        if ($minUsd <= 0) {
            return;
        }

        if ($isUah && (null === $exchangeRate || (float) $exchangeRate <= 0)) {
            throw ValidationException::withMessages(['exchange_rate' => 'Вкажіть курс долара, щоб порахувати оплату годин.']);
        }

        $min = round($minUsd * ($isUah ? (float) $exchangeRate : 1), 2);

        if (round($amount, 2) < $min) {
            $formatted = number_format($min, 2, ',', ' ') . ($isUah ? ' ₴' : ' $');

            throw ValidationException::withMessages(['amount' => "Сума не може бути меншою за оплату годин: {$formatted}."]);
        }
    }
}
