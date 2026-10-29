<?php

namespace App\Containers\AppSection\TimeEntry\Tasks;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Carbon;

final class UpdateTimeEntryTask extends ParentTask
{
    /**
     * Only the user's own finished entries are editable. An entry tracked against a task
     * keeps the task's title, so only free-form entries take a new description.
     *
     * @param array<string, mixed> $data
     */
    public function run(int $id, int $userId, int $projectId, array $data): TimeEntry
    {
        $entry = TimeEntry::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->where('project_id', $projectId)
            ->whereNotNull('stopped_at')
            ->firstOrFail();

        $entry->started_at = Carbon::parse($data['started_at']);
        $entry->stopped_at = Carbon::parse($data['stopped_at']);

        if (null === $entry->task_id && isset($data['description'])) {
            $entry->description = $data['description'];
        }

        $entry->save();

        return $entry;
    }
}
