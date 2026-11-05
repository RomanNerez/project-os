<?php

namespace App\Containers\AppSection\Finance\Tasks;

use App\Containers\AppSection\Finance\Enums\FinanceStatus;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Facades\DB;

final class ListUnpaidFinanceTasksTask extends ParentTask
{
    /**
     * Tasks of the project with tracked time that is not paid yet, one entry per task with its users.
     * Only stopped time entries count; payments of cancelled records are ignored,
     * and so are the payments of $exceptFinanceId (the record being edited), so its time is offered again.
     * The hourly rate (USD) comes from project_user; for pairs already paid by $exceptFinanceId
     * the rate saved in that record wins, the same way SyncFinanceTaskPaymentsTask keeps it.
     *
     * @param int $projectId
     * @param null|string $title
     * @param null|int $exceptFinanceId
     * @param int $limit
     * @return list<array{
     *     task: array{id: int, title: string, project_id: int},
     *     users: list<array{user: User, tracked_seconds: int, paid_seconds: int, hourly_rate: null|string}>
     * }>
     */
    public function run(int $projectId, ?string $title = null, ?int $exceptFinanceId = null, int $limit = 50): array
    {
        $tracked = DB::table('time_entries')
            ->join('tasks', 'tasks.id', '=', 'time_entries.task_id')
            ->where('tasks.project_id', $projectId)
            ->whereNotNull('time_entries.stopped_at')
            ->when(null !== $title && '' !== $title, static fn ($query) => $query->where('tasks.title', 'ilike', '%' . addcslashes((string) $title, '%_\\') . '%'))
            ->groupBy('time_entries.task_id', 'time_entries.user_id')
            ->select([
                'time_entries.task_id',
                'time_entries.user_id',
                DB::raw('SUM(EXTRACT(EPOCH FROM (time_entries.stopped_at - time_entries.started_at)))::bigint AS tracked_seconds'),
            ]);

        $paid = DB::table('finance_task')
            ->join('finances', 'finances.id', '=', 'finance_task.finance_id')
            ->where('finances.status', '<>', FinanceStatus::CANCELLED->value)
            ->when(null !== $exceptFinanceId, static fn ($query) => $query->where('finances.id', '<>', $exceptFinanceId))
            ->groupBy('finance_task.task_id', 'finance_task.user_id')
            ->select([
                'finance_task.task_id',
                'finance_task.user_id',
                DB::raw('SUM(finance_task.seconds)::bigint AS paid_seconds'),
            ]);

        $rows = DB::query()
            ->fromSub($tracked, 't')
            ->join('tasks', 'tasks.id', '=', 't.task_id')
            ->leftJoinSub($paid, 'p', static fn ($join) => $join
                ->on('p.task_id', '=', 't.task_id')
                ->on('p.user_id', '=', 't.user_id'))
            ->leftJoin('project_user', static fn ($join) => $join
                ->on('project_user.project_id', '=', 'tasks.project_id')
                ->on('project_user.user_id', '=', 't.user_id'))
            ->whereRaw('t.tracked_seconds > COALESCE(p.paid_seconds, 0)')
            ->orderByDesc('tasks.created_at')
            ->orderByDesc('tasks.id')
            ->orderByDesc('t.tracked_seconds')
            ->get([
                'tasks.id AS task_id',
                'tasks.title',
                'tasks.project_id',
                't.user_id',
                't.tracked_seconds',
                DB::raw('COALESCE(p.paid_seconds, 0) AS paid_seconds'),
                'project_user.hourly_rate',
            ]);

        $savedRates = null === $exceptFinanceId ? collect() : DB::table('finance_task')
            ->where('finance_id', $exceptFinanceId)
            ->get(['task_id', 'user_id', 'hourly_rate'])
            ->mapWithKeys(static fn ($row): array => ["{$row->task_id}:{$row->user_id}" => $row->hourly_rate]);

        $users = User::query()
            ->with('media')
            ->findMany($rows->pluck('user_id')->unique()->values())
            ->keyBy('id');

        $tasks = [];

        foreach ($rows as $row) {
            $taskId = (int) $row->task_id;

            if (!isset($tasks[$taskId])) {
                if (\count($tasks) >= $limit) {
                    continue;
                }

                $tasks[$taskId] = [
                    'task' => ['id' => $taskId, 'title' => (string) $row->title, 'project_id' => (int) $row->project_id],
                    'users' => [],
                ];
            }

            $key = "{$taskId}:{$row->user_id}";

            $tasks[$taskId]['users'][] = [
                'user' => $users->get((int) $row->user_id),
                'tracked_seconds' => (int) $row->tracked_seconds,
                'paid_seconds' => (int) $row->paid_seconds,
                'hourly_rate' => $savedRates->has($key) ? $savedRates->get($key) : $row->hourly_rate,
            ];
        }

        return array_values($tasks);
    }
}
