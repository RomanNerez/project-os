<?php

namespace App\Containers\AppSection\TimeEntry\Actions;

use App\Containers\AppSection\Task\Tasks\FindVisibleTaskTask;
use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Containers\AppSection\TimeEntry\Tasks\CreateTimeEntryTask;
use App\Containers\AppSection\TimeEntry\Tasks\StopRunningTimeEntriesTask;
use App\Containers\AppSection\TimeEntry\UI\WEB\Requests\StartTimeEntryRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class StartTimeEntryAction extends ParentAction
{
    public function __construct(
        private readonly CreateTimeEntryTask $createTimeEntryTask,
        private readonly StopRunningTimeEntriesTask $stopRunningTimeEntriesTask,
        private readonly FindVisibleTaskTask $findVisibleTaskTask,
    ) {
    }

    /**
     * A tracked task dictates the entry's project and, unless a description is given,
     * its description — whatever project the client sent is ignored.
     *
     * @throws ValidationException
     */
    public function run(StartTimeEntryRequest $request, int $userId): TimeEntry
    {
        $data = $request->validated();
        $now = now()->startOfSecond();
        $taskId = $request->integer('task_id');
        // Empty strings are not converted to null globally, so a blank description is "none".
        $description = $request->filled('description') ? $request->string('description')->toString() : null;
        $task = null;

        if ($taskId > 0) {
            $task = $this->findVisibleTaskTask->run($taskId, $userId)
                ?? throw ValidationException::withMessages(['task_id' => 'Задачу не знайдено.']);
        }

        return DB::transaction(function () use ($data, $now, $userId, $task, $description): TimeEntry {
            $this->stopRunningTimeEntriesTask->run($userId, $now);

            return $this->createTimeEntryTask->run([
                'user_id' => $userId,
                'project_id' => null !== $task ? $task->project_id : ($data['project_id'] ?? null),
                'task_id' => $task?->id,
                'description' => $description ?? $task?->title,
                'started_at' => $now,
                'stopped_at' => null,
            ]);
        });
    }
}
