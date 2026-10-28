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
     * The entry belongs to the project from the URL; a tracked task must be of that project
     * and, unless a description is given, dictates the entry's description.
     *
     * @throws ValidationException
     */
    public function run(StartTimeEntryRequest $request, int $userId, int $projectId): TimeEntry
    {
        $now = now()->startOfSecond();
        $taskId = $request->integer('task_id');
        // Empty strings are not converted to null globally, so a blank description is "none".
        $description = $request->filled('description') ? $request->string('description')->toString() : null;
        $task = null;

        if ($taskId > 0) {
            $task = $this->findVisibleTaskTask->run($taskId, $userId);

            if (null === $task || (int) $task->project_id !== $projectId) {
                throw ValidationException::withMessages(['task_id' => 'Задачу не знайдено.']);
            }
        }

        return DB::transaction(function () use ($now, $userId, $projectId, $task, $description): TimeEntry {
            $this->stopRunningTimeEntriesTask->run($userId, $now);

            return $this->createTimeEntryTask->run([
                'user_id' => $userId,
                'project_id' => $projectId,
                'task_id' => $task?->id,
                'description' => $description ?? $task?->title,
                'started_at' => $now,
                'stopped_at' => null,
            ]);
        });
    }
}
