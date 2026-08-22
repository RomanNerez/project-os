<?php

namespace App\Containers\AppSection\TimeEntry\Tasks;

use App\Containers\AppSection\TimeEntry\Models\TimeEntry;
use App\Ship\Parents\Tasks\Task as ParentTask;

final class DeleteTimeEntryTask extends ParentTask
{
    public function run(int $id, int $userId): bool
    {
        return (bool) TimeEntry::query()
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();
    }
}
